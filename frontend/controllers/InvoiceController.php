<?php

namespace frontend\controllers;

use common\models\Barcode;
use common\models\BarcodeTemp;
use common\models\Company;
use common\models\InvoiceDebtHistory;
use common\models\InvoiceItems;
use common\models\Product;
use Exception;
use frontend\models\forms\InvoiceDebtHistoryForm;
use frontend\models\InvoiceForm;
use phpDocumentor\Reflection\Types\String_;
use Picqer\Barcode\BarcodeGeneratorJPG;
use Yii;
use common\models\Invoice;
use frontend\models\InvoiceSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\MultipleModel as Model;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['manageInvoice']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'check-product', 'add-debt', 'get-checked-random-barcode'],
                        'roles' => ['createInvoice']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['updateInvoice']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['viewInvoice']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteInvoice']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['main'],
                        'roles' => ['manageInvoice']
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Invoice models.
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

    public function actionMain()
    {
        return $this->render('main');
    }

    /**
     * Displays a single Invoice model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $modelInvoice = $this->findModel($id);
        $modelsInvoiceItems = $modelInvoice->invoiceItems;

        return $this->render('view', [
            'modelInvoice' => $modelInvoice,
            'modelsInvoiceItem' => (empty($modelsInvoiceItems)) ? [new InvoiceItems()] : $modelsInvoiceItems
        ]);
    }

    /**
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws Exception
     */
    public function actionCreate()
    {
        $modelInvoice = new InvoiceForm();
        $modelsInvoiceItem = [new InvoiceItems()];
        if ($modelInvoice->load(Yii::$app->request->post())) {
            $modelsInvoiceItem = Model::createMultiple(InvoiceItems::classname());
            Model::loadMultiple($modelsInvoiceItem, Yii::$app->request->post());

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsInvoiceItem),
                    ActiveForm::validate($modelInvoice)
                );
            }

            // validate all models
            $valid = $modelInvoice->validate();
            $valid = Model::validateMultiple($modelsInvoiceItem) && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $itemsCost = 0;
                    foreach ($modelsInvoiceItem as $item) {
                        /** @var InvoiceItems $item */
                        $itemsCost += $item->price_in * $item->quantity;
                    }
                    $modelInvoice->cost = $itemsCost;

                    if ($flag = $modelInvoice->save()) {
                        $modelInvoice->id = $flag;
                        /** @var InvoiceItems $modelInvoiceItem */
                        foreach ($modelsInvoiceItem as $k => $modelInvoiceItem) {
                            $modelInvoiceItem->invoice_id = $modelInvoice->id;

                            // Если товар новый или его нет в базе то его добавляем в таблицу баркод темп для дальнейшего его рассмотрение адином
                            if ($modelInvoiceItem->is_new) {
                                $barcodeTemp = new BarcodeTemp();
                                $barcodeTemp->number = $modelInvoiceItem->barcode;
                                $barcodeTemp->name = $modelInvoiceItem->name;
                                $barcodeTemp->is_partial = $modelInvoiceItem->is_partial;
                                if (!$barcodeTemp->save())
                                    throw new Exception("Local barcode is not saved");
                            }

                            // Добавляем товары инвоиса на склад
                            $product = Product::findOne(['barcode' => $modelInvoiceItem->barcode]);

                            if ($product) {
                                $product->quantity += $modelInvoiceItem->quantity;
                            } else {
                                $product = new Product();
                                $product->barcode = $modelInvoiceItem->barcode;
                                $product->name = $modelInvoiceItem->name;
                                $product->quantity = $modelInvoiceItem->quantity;
                                $product->percentage_rate = $modelInvoiceItem->percentage_rate;
                                $product->price_retail = $modelInvoiceItem->price_retail;
                                $product->is_partial = $modelInvoiceItem->is_partial;
                                $product->price_wholesale = $modelInvoiceItem->wholesale_price;
                                $product->wholesale_value = $modelInvoiceItem->wholesale_value;
                                $product->status = Product::STATUS_ACTIVE;
                            }

                            if (!$product->save())
                                throw new Exception("Product is not saved");

                            if (! ($flag = $modelInvoiceItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        $sum = $itemsCost - $modelInvoice->paid_amount;
                        Yii::$app->settings->setBalance($sum, true);
                    }

                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['index', 'id' => $modelInvoice->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }
        }

        return $this->render('create', [
            'modelInvoice' => $modelInvoice,
            'modelsInvoiceItem' => (empty($modelsInvoiceItem)) ? [new InvoiceItems()] : $modelsInvoiceItem
        ]);
    }

    /**
     * Updates an existing Invoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $modelInvoice = $this->findModel($id);
        $modelsInvoiceItems = $modelInvoice->invoiceItems;

        if ($modelInvoice->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsInvoiceItems, 'id', 'id');
            $modelsInvoiceItems = Model::createMultiple(InvoiceItems::classname(), $modelsInvoiceItems);
            Model::loadMultiple($modelsInvoiceItems, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsInvoiceItems, 'id', 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsInvoiceItems),
                    ActiveForm::validate($modelInvoice)
                );
            }

            // validate all models
            $valid = $modelInvoice->validate();
            $valid = Model::validateMultiple($modelsInvoiceItems) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $modelInvoice->save(false)) {
                        if (! empty($deletedIDs)) {
                            InvoiceItems::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsInvoiceItems as $modelInvoiceItem) {
                            $modelInvoiceItem->invoice_id = $modelInvoice->id;
                            if (! ($flag = $modelInvoiceItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['index', 'id' => $modelInvoice->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'modelInvoice' => $modelInvoice,
            'modelsInvoiceItem' => (empty($modelsInvoiceItems)) ? [new InvoiceItems()] : $modelsInvoiceItems
        ]);
    }

    /**
     * Deletes an existing Invoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionCheckProduct()
    {
        if (Yii::$app->request->isAjax) {
            $generator = new BarcodeGeneratorJPG();
            $checked_barcode = null;

            $barcode = Yii::$app->request->post('barcode');
            $product = Product::findOne(['barcode' => $barcode]);

            if ($product) {
                /** @var Product $product */
                $product_array = [
                    'name' => $product->name,
                    'barcode' => $barcode
                ];
            } else {
                $product = Barcode::findOne(['number' => $barcode]);
                $product_name = $product->name;

                if (!$product) {
                    $product_name = null;
                }

                $product_array = [
                    'name' => $product_name,
                    'barcode' => $barcode,
                    'is_exist' => true
                ];
            }

            return Json::encode($product_array);
        }

        return false;
    }

    public function actionGetCheckedRandomBarcode()
    {
        if (Yii::$app->request->isAjax) {
            do {
                $barcode = '999' . rand(1, 99999999999);
                $product = Product::findOne(['barcode' => $barcode]);
            } while ($product);
            return $barcode;
        }
        return false;
    }

    public function actionAddDebt()
    {
        if (Yii::$app->request->isAjax) {
            $invoice_id = Yii::$app->request->post('id');

            $model = new InvoiceDebtHistoryForm();
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->settings->setBalance($model->paid_amount, true);
                return true;
            }

            /** @var Invoice $invoice */
            $invoice = Invoice::findOne(['id' => $invoice_id, 'is_debt' => Invoice::STATUS_IS_DEBT_ACTIVE]);

            return $this->renderAjax('_add-debt', [
                'model' => $model,
                'invoice' => $invoice
            ]);
        }
        
        return false;
    }


    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
