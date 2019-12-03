<?php


namespace console\controllers;


use common\models\Order;
use yii\console\Controller;
use yii\httpclient\Client;

class ObjectController extends Controller
{
    public function actionClearCache()
    {
        \Yii::$app->settings->clearCache();
    }
}