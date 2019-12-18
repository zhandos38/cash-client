<?php

namespace console\controllers;


use common\models\Barcode;
use common\models\BarcodeTemp;
use common\models\Customer;
use common\models\Invoice;
use common\models\Log;
use common\models\Order;
use common\models\Product;
use common\models\ShiftHistory;
use common\models\Supplier;
use common\models\User;
use pheme\settings\models\Setting;
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
//    const TARGET_BARCODE = 'barcode';
    const TARGET_BARCODE_TEMP = 'barcode-temp';
    const TARGET_SHIFT = 'shifts';
    const TARGET_STAFF = 'staff';
    const TARGET_SETTINGS = 'settings';
    const TARGET_EXPIRE_DATE = 'get-expire-date';

    public function actionStaff()
    {
        $started_at = time();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var Supplier $suppliers */
            $staff = User::find()
                ->where('is_sent = false or updated_at > exported_at')
                ->all();

            if (!$staff) {
                throw new \Exception('Staff has been already updated!');
            }

            foreach ($staff as $item) {
                if (!$item->is_sent) {
                    $item->is_sent = true;
                }
                $item->detachBehavior('timestamp');
                $item->exported_at = time();

                $item->save();

                $data = ArrayHelper::toArray($staff, [
                    'common\models\User' => [
                        'id',
                        'full_name',
                        'phone',
                        'code_number',
                        'role',
                        'status',
                        'created_at'
                    ]
                ]);
            }

            if ($this->send($data, self::TARGET_STAFF)) {
                $transaction->commit();
                Log::createLog(Log::SOURCE_EXPORT_STAFF, 'Staff is exported successfully!', Log::STATUS_SUCCESS, $started_at);
                $this->log(true);
            } else {
                throw new \Exception('Staff is not sent');
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            Log::createLog(Log::SOURCE_EXPORT_STAFF, $exception->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            throw new Exception($exception->getMessage());
        }

        return true;
    }

    public function actionOrders()
    {
        $started_at = time();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var Order $orders */
            $orders = Order::find()->where(['is_sent' => false])->all();

            if (!$orders) {
                throw new \Exception('All orders have been already sent!');
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
                Log::createLog(Log::SOURCE_EXPORT_ORDER, 'Order is exported successfully!', Log::STATUS_SUCCESS, $started_at);
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
                throw new \Exception('All invoices have been already sent!');
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
                Log::createLog(Log::SOURCE_EXPORT_INVOICE, 'Invoice is exported successfully!', Log::STATUS_SUCCESS, $started_at);
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
                throw new \Exception('All product have been already updated!');
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
                Log::createLog(Log::SOURCE_EXPORT_PRODUCT, 'Product is exported successfully!', Log::STATUS_SUCCESS, $started_at);
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
                throw new \Exception('All customers have been already updated!');
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
                Log::createLog(Log::SOURCE_EXPORT_CUSTOMER, 'Customer is exported successfully!', Log::STATUS_SUCCESS, $started_at);
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
                throw new \Exception('All suppliers have been already updated!');
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
                Log::createLog(Log::SOURCE_EXPORT_SUPPLIER, 'Suppliers is exported successfully!', Log::STATUS_SUCCESS, $started_at);
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
                throw new \Exception('All shift have been already updated!');
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
                Log::createLog(Log::SOURCE_EXPORT_SHIFT, 'Shift is exported successfully!', Log::STATUS_SUCCESS, $started_at);
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

    public function actionBarcodeTemp()
    {
        $started_at = time();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var BarcodeTemp $barcodes */
            $barcodes = BarcodeTemp::find()->where('is_sent = false')->all();

            if (!$barcodes) {
                throw new \Exception('All temp barcodes have been already updated!');
            }

            foreach ($barcodes as $barcode) {
                if (!$barcode->is_sent) {
                    $barcode->is_sent = true;
                }
                $barcode->save();

                $data = ArrayHelper::toArray($barcodes, [
                    'common\models\BarcodeTemp' => [
                        'id',
                        'number',
                        'name',
                        'is_partial'
                    ]
                ]);
            }

            if ($this->send($data, self::TARGET_BARCODE_TEMP)) {
                $transaction->commit();
                Log::createLog(Log::SOURCE_EXPORT_BARCODE_TEMP, 'Barcode temp is exported successfully!', Log::STATUS_SUCCESS, $started_at);
                $this->log(true);
            } else {
                throw new \Exception('Barcode temp is not sent');
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            Log::createLog(Log::SOURCE_EXPORT_BARCODE_TEMP, $exception->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            throw new Exception($exception->getMessage());
        }

        return true;
    }

    public function actionSettings()
    {
        $settings = Setting::findAll(['is_updated' => false]);

        if (!$settings)
            throw new \Exception('All settings have been already updated!');

        $started_at = time();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $data = [];
            /** @var Setting $setting */
            foreach ($settings as $setting) {
                if (!$setting->is_updated) {
                    $setting->is_updated = true;
                }
                $setting->save();

                $data[$setting->key] = $setting->value;
            }

            if ($this->send($data, self::TARGET_SETTINGS)) {
                $transaction->commit();
                Log::createLog(Log::SOURCE_EXPORT_SETTINGS, 'Settings is exported successfully!', Log::STATUS_SUCCESS, $started_at);
                $this->log(true);
            } else {
                throw new \Exception('Settings is not sent');
            }
        } catch (\Exception $exception) {
            $transaction->rollBack();
            Log::createLog(Log::SOURCE_EXPORT_SETTINGS, $exception->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            throw new Exception($exception->getMessage());
        }

        return true;
    }

    public function actionCheckExpireDate()
    {
        $started_at = time();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $token = \Yii::$app->settings->getToken();
            $expirationDate = Yii::$app->settings->getExpiredAt();

            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl(\Yii::$app->params['apiUrl'] . 'v1/' . self::TARGET_EXPIRE_DATE)
                ->addHeaders(['Authorization' => 'Bearer ' . $token])
                ->addHeaders(['content-type' => 'application/json'])
                ->setData(['token' => $token])
                ->send();

            if ($response->content != $expirationDate) {
                Yii::$app->settings->setExpiredAt($response->content);
            }

            $transaction->commit();
            Log::createLog(Log::SOURCE_CHECK_EXPIRE_DATE, 'Check expire date is checked successfully!', Log::STATUS_SUCCESS, $started_at);
            $this->log(true);

        } catch (\Exception $exception) {
            $transaction->rollBack();
            Log::createLog(Log::SOURCE_CHECK_EXPIRE_DATE, $exception->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            throw new Exception($exception->getMessage());
        }
    }

    private function send($data, $target)
    {
//        Yii::$app->settings->setToken('0gdvpk3i308ZljkbzpRQIxj1HWMEb--v');
        $token = \Yii::$app->settings->getToken();

        $fp = fopen("c:/ProgramData/test.txt", "a+");
        fwrite($fp, VarDumper::dumpAsString($data,10));
        fclose($fp);

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl(\Yii::$app->params['apiUrl'] . 'v1/' . $target)
            ->addHeaders(['Authorization' => 'Bearer ' . $token])
            ->addHeaders(['content-type' => 'application/json'])
            ->setData(['token' => $token, 'data' => $data])
            ->send();

        $fp = fopen("c:/ProgramData/test.txt", "a+");
        fwrite($fp, VarDumper::dumpAsString($response->content, 10));
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