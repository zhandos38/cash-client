<?php

namespace console\controllers;


use common\models\Order;
use yii\console\Controller;
use yii\helpers\Json;
use yii\httpclient\Client;

/**
 * Class ExportController
 * @package console\controllers
 */
class ExportController extends Controller
{
    public function actionOrders()
    {
        $data = [];
        $token = \Yii::$app->settings->getToken();
        $orders = Order::find()->where(['is_sent' => false])->asArray()->all();

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://api.cash/v1/orders')
            ->addHeaders(['Authorization' => 'Bearer ' . $token])
            ->addHeaders(['content-type' => 'application/json'])
            ->setData(['token' => $token, 'orders' => $orders])
            ->send();

        $fp = fopen("c:/test.txt", "a+");
        fwrite($fp, $response->content);
        fclose($fp);
    }
}