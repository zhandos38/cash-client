<?php


namespace backend\controllers;


use backend\models\InvoiceSearch;
use backend\models\OrderSearch;
use Yii;
use yii\web\Controller;

class InvoiceController extends Controller
{
    /**
     * Lists all Warehouse models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}