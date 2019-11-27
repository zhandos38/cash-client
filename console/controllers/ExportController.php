<?php

namespace console\controllers;


use common\models\Customer;
use common\models\Invoice;
use common\models\Log;
use common\models\Order;
use common\models\Product;
use common\models\ShiftHistory;
use common\models\Supplier;
use Yii;
use yii\console\Controller;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\httpclient\Client;

/**
 * Class ExportController
 * @package console\controllers
 */
class ExportController extends Controller
{
    const TARGET_ORDERS = 'orders';
    const TARGET_INVOICES = 'invoices';
    const TARGET_CUSTOMERS = 'customers';
    const TARGET_SUPPLIERS = 'suppliers';
    const TARGET_PRODUCT = 'product';
    const TARGET_BARCODE = 'barcode';
    const TARGET_BARCODE_TEMP = 'temp-barcode';
    const TARGET_SHIFT = 'shifts';

    public function actionOrders()
    {
        $started_at = time();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var Order $orders */
            $orders = Order::find()->where(['is_sent' => false])->all();

            if (!$orders) {
                throw new \Exception('All orders already have been sent!');
            }

            foreach ($orders as $order) {
                $order->is_sent = true;
                $order->save();

                $data = ArrayHelper::toArray($orders, [
                    'common\models\Order' => [
                        'number',
                        'created_by',
                        'customer_id',
                        'cost',
                        'total_cost',
                        'taken_cash',
                        'pay_id',
                        'pay_status',
                        'status',
                        'is_debt',
                        'shift_id',
                        'comment',
                        'is_sent',
                        'created_at',
                        'updated_at',
                        'items' => function(Order $model) {
                            return $model->orderItems;
                        }
                    ]
                ]);
            }
            if ($this->send($data, self::TARGET_ORDERS)) {
                $transaction->commit();
                Log::createLog(Log::SOURCE_EXPORT_ORDER, 'Order exporting success!', Log::STATUS_SUCCESS, $started_at);
                $this->log(true);
            } else {
                throw new \Exception('Orders is not sent');
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            Log::createLog(Log::SOURCE_EXPORT_ORDER, $exception->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            $this->log(false);
            throw new \Exception($exception->getMessage());
        }

        return true;
    }

    public function actionInvoices()
    {
        $started_at = time();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var Invoice $invoices */
            $invoices = Invoice::find()->where(['is_sent' => false])->all();

            if (!$invoices) {
                throw new \Exception('All invoices already have been sent!');
            }

            foreach ($invoices as $invoice) {
                $invoice->is_sent = true;
                $invoice->save();

                $data = ArrayHelper::toArray($invoices, [
                    'common\models\Invoice' => [
                        'id',
                        'number_in',
                        'is_debt',
                        'status',
                        'created_by',
                        'created_at',
                        'supplier_id',
                        'cost',
                        'items' => function(Invoice $model) {
                            return $model->invoiceItems;
                        }
                    ]
                ]);
            }

            if ($this->send($data, self::TARGET_INVOICES)) {
                $transaction->commit();
                Log::createLog(Log::SOURCE_EXPORT_INVOICE, 'Invoice exporting success!', Log::STATUS_SUCCESS, $started_at);
                $this->log(true);
            } else {
                throw new \Exception('Invoices is not sent');
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            Log::createLog(Log::SOURCE_EXPORT_INVOICE, $exception->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            $this->log(false);
            throw new \Exception($exception->getMessage());
        }

        return true;
    }

    public function actionProducts()
    {
        $started_at = time();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var Invoice $invoices */
            $products = Product::find()->where('updated_at > exported_at')->all();

            if (!$products) {
                throw new \Exception('All product already have been updated!');
            }

            foreach ($products as $product) {
                if (!$product->is_sent) {
                    $product->is_sent = true;
                }
                $product->detachBehavior('timestamp');
                $product->exported_at = time();
                $product->save();

                $data = ArrayHelper::toArray($products, [
                    'common\models\Product' => [
                        'barcode',
                        'name',
                        'quantity',
                        'price_wholesale',
                        'price_retail',
                        'percentage_rate',
                        'wholesale_value',
                        'is_partial',
                        'status',
                        'created_at',
                        'updated_at',
                        'is_favourite'
                    ]
                ]);
            }

            if ($this->send($data, self::TARGET_PRODUCT)) {
                $transaction->commit();
                Log::createLog(Log::SOURCE_EXPORT_PRODUCT, 'Product exported success!', Log::STATUS_SUCCESS, $started_at);
                $this->log(true);
            } else {
                throw new \Exception('Product is not sent');
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            Log::createLog(Log::SOURCE_EXPORT_PRODUCT, $exception->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            throw new Exception($exception->getMessage());
        }

        return true;
    }

    public function actionCustomers()
    {
        $started_at = time();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var Invoice $invoices */
            $customers = Customer::find()->where('is_sent = false or updated_at > exported_at')->all();

            if (!$customers) {
                throw new \Exception('All customers already have been updated!');
            }

            foreach ($customers as $customer) {
                if (!$customer->is_sent) {
                    $customer->is_sent = true;
                }
                $customer->detachBehavior('timestamp');
                $customer->exported_at = time();

                $customer->save();

                $data = ArrayHelper::toArray($customers, [
                    'common\models\Customer' => [
                        'id',
                        'full_name',
                        'phone',
                        'address',
                        'birthday_date',
                        'card_number',
                        'discount_id',
                        'is_discount_limited',
                        'discount_value',
                        'discount_quantity',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ]);
            }

            if ($this->send($data, self::TARGET_CUSTOMERS)) {
                $transaction->commit();
                Log::createLog(Log::SOURCE_EXPORT_CUSTOMER, 'Customer exported success!', Log::STATUS_SUCCESS, $started_at);
                $this->log(true);
            } else {
                throw new \Exception('Customer is not sent');
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            Log::createLog(Log::SOURCE_EXPORT_CUSTOMER, $exception->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            throw new Exception($exception->getMessage());
        }

        return true;
    }

    public function actionSuppliers()
    {
        $started_at = time();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var Supplier $suppliers */
            $suppliers = Supplier::find()->where('is_sent = false or updated_at > exported_at')->all();

            if (!$suppliers) {
                throw new \Exception('All suppliers already have been updated!');
            }

            foreach ($suppliers as $supplier) {
                if (!$supplier->is_sent) {
                    $supplier->is_sent = true;
                }
                $supplier->detachBehavior('timestamp');
                $supplier->exported_at = time();

                $supplier->save();

                $data = ArrayHelper::toArray($suppliers, [
                    'common\models\Supplier' => [
                        'id',
                        'name',
                        'created_at'
                    ]
                ]);
            }

            if ($this->send($data, self::TARGET_SUPPLIERS)) {
                $transaction->commit();
                Log::createLog(Log::SOURCE_EXPORT_SUPPLIER, 'Suppliers exported success!', Log::STATUS_SUCCESS, $started_at);
                $this->log(true);
            } else {
                throw new \Exception('Suppliers is not sent');
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            Log::createLog(Log::SOURCE_EXPORT_SUPPLIER, $exception->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            throw new Exception($exception->getMessage());
        }

        return true;
    }

    public function actionShifts()
    {
        $started_at = time();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var Supplier $suppliers */
            $shifts = ShiftHistory::find()->where('is_sent = false and closed_at > 0')->all();

            if (!$shifts) {
                throw new \Exception('All shift already have been updated!');
            }

            foreach ($shifts as $shift) {
                if (!$shift->is_sent) {
                    $shift->is_sent = true;
                }
                $shift->save();

                $data = ArrayHelper::toArray($shifts, [
                    'common\models\ShiftHistory' => [
                        'id',
                        'user_id',
                        'status',
                        'started_at',
                        'closed_at',
                        'transactions' => function(ShiftHistory $model) {
                            return $model->transactions;
                        }
                    ]
                ]);
            }

            if ($this->send($data, self::TARGET_SHIFT)) {
                $transaction->commit();
                Log::createLog(Log::SOURCE_EXPORT_SHIFT, 'Shift exported success!', Log::STATUS_SUCCESS, $started_at);
                $this->log(true);
            } else {
                throw new \Exception('Shift is not sent');
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            Log::createLog(Log::SOURCE_EXPORT_SHIFT, $exception->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            throw new Exception($exception->getMessage());
        }

        return true;
    }

    private function send($data, $target)
    {
        Yii::$app->settings->setToken('0gdvpk3i308ZljkbzpRQIxj1HWMEb--v');
        $token = \Yii::$app->settings->getToken();

        $fp = fopen("c:/test.txt", "a+");
        fwrite($fp, VarDumper::dumpAsString($data,10));
        fclose($fp);

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl(\Yii::$app->params['apiUrlDev'] . 'v1/' . $target)
            ->addHeaders(['Authorization' => 'Bearer ' . $token])
            ->addHeaders(['content-type' => 'application/json'])
            ->setData(['token' => $token, 'data' => $data])
            ->send();

        $fp = fopen("c:/test.txt", "a+");
        fwrite($fp, $response->content);
        fclose($fp);

        if (!$response->isOk) {
            return false;
        }
        return true;
    }

    /**
     * @param $success
     * @param string $message
     */
    private function log($success, $message = '')
    {
        if ($success) {
            $this->stdout('Success! ' . $message);
        } else {
            $this->stderr('Error! ' . $message);
        }
        echo PHP_EOL;
    }
}