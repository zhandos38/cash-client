<?php

namespace console\controllers;


use common\models\Invoice;
use common\models\Log;
use common\models\Order;
use common\models\Product;
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
    const TARGET_SHIFT = 'shift';

    public function actionOrders()
    {
        $started_at = time();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var Order $orders */
            $orders = Order::find()->where(['is_sent' => false])->all();

            if (!$orders) {
                $this->log(false, 'All orders already have sent!');
                return false;
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
        } catch (\Exception $excectpion) {
            $transaction->rollBack();
            Log::createLog(Log::SOURCE_EXPORT_ORDER, $excectpion->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            $this->log(false);
            throw new Exception($excectpion->getMessage());
        }

        return true;
    }

    public function actionInvoices()
    {
        $started_at = time();
        try {
            /** @var Invoice $invoices */
            $invoices = Invoice::find()->where(['is_sent' => false])->all();

            if (!$invoices)
                return false;

            foreach ($invoices as $invoice) {
                $invoice->is_sent = true;
                $invoice->save();

                $data = ArrayHelper::toArray($invoices, [
                    'common\models\Invoices' => [
                        'number_in',
                        'is_debt',
                        'status',
                        'created_by',
                        'created_at',
                        'cost',
                        'items' => function(Invoice $model) {
                            return $model->invoiceItems;
                        }
                    ]
                ]);
            }

            $this->send($data, self::TARGET_INVOICES);
            Log::createLog(Log::SOURCE_EXPORT_ORDER, 'Invoice exporting success!', Log::STATUS_SUCCESS, $started_at);
        } catch (\Exception $excectpion) {
            Log::createLog(Log::SOURCE_EXPORT_ORDER, $excectpion->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            throw new Exception($excectpion->getMessage());
        }

        return true;
    }

    public function actionProduct()
    {
        $started_at = time();
        try {
            /** @var Invoice $invoices */
            $products = Product::find()->where(['>', 'updated_at', 'exported_at'])->all();

            if (!$products)
                return false;

            foreach ($products as $product) {
                $product->exported_at = time();
                $product->save();

                $data = ArrayHelper::toArray($products, [
                    'common\models\Product' => [
                        'barcode',
                        'name',
                        'quantity',
                        'price_wholesale',
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

            $this->send($data, self::TARGET_PRODUCT);
            Log::createLog(Log::SOURCE_EXPORT_PRODUCT, 'Product exporting success!', Log::STATUS_SUCCESS, $started_at);
        } catch (\Exception $excectpion) {
            Log::createLog(Log::SOURCE_EXPORT_PRODUCT, $excectpion->getMessage(), Log::STATUS_EXCEPTION, $started_at);
            throw new Exception($excectpion->getMessage());
        }

        return true;
    }

    private function send($data, $target)
    {
        $token = \Yii::$app->settings->getToken();

        $fp = fopen("c:/test.txt", "a+");
        fwrite($fp, Json::encode($data));
        fclose($fp);

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl(\Yii::$app->params['apiUrl'] . 'v1/' . $target)
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