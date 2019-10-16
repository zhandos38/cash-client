<?php


namespace backend\controllers;


use backend\models\BarcodeSearch;
use backend\models\WarehouseSearch;
use Yii;
use yii\web\Controller;

class WarehouseController extends Controller
{
    /**
     * Lists all Warehouse models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WarehouseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}