<?php


namespace console\controllers;


use common\models\Order;
use yii\console\Controller;
use yii\httpclient\Client;

class SiteController extends Controller
{
    public function actionOrders()
    {
        $data = [];
        $token = \Yii::$app->settings->getToken();
//        $orders = Order::find()->all();
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://api.cash/v1/orders')
            ->addHeaders(['Authorization' => 'Bearer ' . $token])
            ->addHeaders(['content-type' => 'application/json'])
            ->setData(['token' => $token, 'data' => 'xa'])
            ->send();
    }
}