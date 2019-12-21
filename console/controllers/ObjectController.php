<?php


namespace console\controllers;


use common\models\Order;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Yii;
use yii\console\Controller;
use yii\httpclient\Client;

class ObjectController extends Controller
{
    public function actionClearCache()
    {
        \Yii::$app->settings->clearCache();
    }

    public function actionOpenCashDraw()
    {
        $connector = new NetworkPrintConnector("192.168.1.87", 9100);
        $printer = new Printer($connector);
        $printer -> pulse(0, 120, 240);
        $printer -> close();

        return false;
    }
}