<?php

namespace api\modules\v1\controllers;

use common\components\auth\CustomHttpBearerAuth;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;


class ObjectController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['authMethods'] = [
            HttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function actionActivate()
    {
        return 'it works';
    }
}