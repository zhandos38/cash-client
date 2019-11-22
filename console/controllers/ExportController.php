<?php

namespace console\controllers;


use common\models\Order;
use yii\console\Controller;
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
    public function actionOrders()
    {
        /** @var Order $orders */
        $orders = Order::find()->where(['is_sent' => false])->all();

        if (!$orders)
            return false;

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
                    'products' => function(Order $model) {
                        return $model->orderItems;
                    }
                ]
            ]);
        }

        $this->send($data);

        return true;
    }

    private function send($data)
    {
        $token = \Yii::$app->settings->getToken();

        $fp = fopen("c:/test.txt", "a+");
        fwrite($fp, Json::encode($data));
        fclose($fp);

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl(\Yii::$app->params['apiUrl'] . 'v1/orders')
            ->addHeaders(['Authorization' => 'Bearer ' . $token])
            ->addHeaders(['content-type' => 'application/json'])
            ->setData(['token' => $token, 'data' => $data])
            ->send();

        $fp = fopen("c:/test.txt", "a+");
        fwrite($fp, $response->content);
        fclose($fp);
    }
}