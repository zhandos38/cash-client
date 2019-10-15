<?php

namespace frontend\controllers;

use common\models\BarcodeTemp;
use common\models\Customer;
use common\models\Invoice;
use common\models\InvoiceItems;
use common\models\OrderItems;
use common\models\Product;
use Exception;
use frontend\models\AddInvoiceForm;
use frontend\models\forms\CustomerForm;
use frontend\models\forms\InvoiceDebtHistoryForm;
use frontend\models\forms\OrderDebtHistoryForm;
use frontend\models\MultipleModel as Model;
use frontend\models\OrderForm;
use Yii;
use common\models\Order;
use frontend\models\OrderSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
                        'roles' => ['manageOrder']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'customer-list', 'add-customer', 'get-product-by-id', 'add-debt'],
                        'roles' => ['createOrder']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['viewOrder']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['updateOrder']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteOrder']
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
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Customer models.
     * @return mixed
     */
    public function actionCustomerList()
    {
        $data = [];
        if (Yii::$app->request->isAjax) {
            $model = new Customer();

            if (Yii::$app->request->post()) {
                $request = Yii::$app->request->post('Customer');
                if ($request['full_name'] || $request['phone']) {
                    $data = Customer::find()
                        ->select(['id', 'full_name', 'phone', 'address'])
                        ->andFilterWhere(['like', 'full_name', $request['full_name']])
                        ->andFilterWhere(['like', 'phone', $request['phone']])
                        ->asArray()
                        ->all();
                }
                return Json::encode($data);
            }

            return $this->renderAjax('customer-list', [
                'model' => $model,
            ]);
        }

        return false;
    }

    public function actionAddCustomer() {
        if (Yii::$app->request->isAjax){
            $model = new CustomerForm();

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return true;
            }

            return $this->renderAjax('customer-form', [
                'model' => $model
            ]);
        }
        return false;
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $company_id = Yii::$app->user->identity->company_id;
        $modelOrder = new OrderForm();
        $modelsOrderItem = [new OrderItems()];
        if ($modelOrder->load(Yii::$app->request->post())) {
            $modelsOrderItem = Model::createMultiple(OrderItems::classname());
            Model::loadMultiple($modelsOrderItem, Yii::$app->request->post());

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsOrderItem),
                    ActiveForm::validate($modelOrder)
                );
            }

            // validate all models
            $valid = $modelOrder->validate();
            $valid = Model::validateMultiple($modelsOrderItem) && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $itemsCost = 0;
                    foreach ($modelsOrderItem as $item) {
                        /** @var OrderItems $item */
                        $itemsCost += $item->real_price * $item->quantity;
                    }
                    $modelOrder->cost = $itemsCost;

                    if ($flag = $modelOrder->save()) {
                        $modelOrder->id = $flag;
                        /** @var OrderItems $modelOrderItem */
                        foreach ($modelsOrderItem as $k => $modelOrderItem) {
                            $modelOrderItem->order_id = $modelOrder->id;

                            // Добавляем товары инвоиса на склад
                            $product = Product::findOne(['barcode' => $modelOrderItem->barcode, 'company_id' => $company_id]);
                            $product->quantity -= $modelOrderItem->quantity;

                            if (!$product->save())
                                throw new Exception("Order product is not updated");

                            if (! ($flag = $modelOrderItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        Yii::$app->user->identity->company->updateBalance($itemsCost - $modelOrder->paid_amount);
                    }

                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['index', 'id' => $modelOrder->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }
        }

        return $this->render('create', [
            'modelOrder' => $modelOrder,
            'modelsOrderItem' => (empty($modelsOrderItem)) ? [new OrderItems()] : $modelsOrderItem
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Order model.
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

    public function actionGetProductById()
    {
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            $product = Product::find()->select(['id', 'name', 'barcode', 'price_retail', 'is_partial'])->where(['id' => $id])->asArray()->one();
            return Json::encode($product);
        }
        return false;
    }

    public function actionGetProductByBarcode()
    {
        if (Yii::$app->request->isAjax) {
            $barcode = Yii::$app->request->post('barcode');
            $product = Product::find()->select(['id', 'name', 'barcode', 'price_retail', 'is_partial'])->where(['barcode' => $barcode])->asArray()->one();
            return Json::encode($product);
        }
        return false;
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAddDebt()
    {
        $order_id = Yii::$app->request->post('id');

        $model = new OrderDebtHistoryForm();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->user->identity->company->updateBalance($model->paid_amount);
            return true;
        }

        /** @var Invoice $invoice */
        $order = Order::findOne(['id' => $order_id, 'is_debt' => Order::IS_DEBT_STATUS_YES]);

        return $this->renderAjax('_add-debt', [
            'model' => $model,
            'order' => $order
        ]);
    }
}
