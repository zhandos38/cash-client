<?php
namespace app\controllers;


use yii\rest\Controller;

/**
 * Class SiteController
 * @package api\controllers
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        return [
            'version' => '1.0.0',
        ];
    }
}