<?php

namespace frontend\controllers;

use common\models\Customer;
use common\models\Invoice;
use common\models\OrderDebtHistory;
use common\models\OrderItems;
use common\models\Product;
use Exception;
use frontend\assets\CashDrawAsset;
use frontend\models\forms\CustomerForm;
use frontend\models\forms\OrderDebtHistoryForm;
use frontend\models\MultipleModel as Model;
use frontend\models\OrderForm;
use frontend\models\OrderTestForm;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Yii;
use common\models\Order;
use frontend\models\OrderSearch;
use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

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
                        'actions' => ['create', 'customer-list', 'add-customer', 'get-product-by-id', 'get-product-by-barcode', 'add-debt', 'test-create', 'pay-order', 'test-customer-list', 'open-cash-draw', 'search'],
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
                    [
                        'allow' => true,
                        'actions' => ['main'],
                        'roles' => ['manageOrder']
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

    public function actionMain()
    {
        return $this->render('main');
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

    /**
     * Lists all Customer models.
     * @return mixed
     */
    public function actionTestCustomerList()
    {
        $data = [];
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $request = Yii::$app->request->post();
            if ($request['name'] || $request['phone']) {
                $data = Customer::find()
                    ->select(['id', 'full_name', 'phone', 'address'])
                    ->andFilterWhere(['like', 'full_name', $request['name']])
                    ->andFilterWhere(['like', 'phone', $request['phone']])
                    ->asArray()
                    ->all();
            }
            return $data;
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
     * @throws Exception
     */
    public function actionCreate()
    {
        $this->layout = 'order';

        /** @var OrderForm $modelOrder */
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
                    $modelOrder->number = Order::generateNumber();
                    $modelOrder->shift_id = Yii::$app->object->getShiftId();

                    if ($flag = $modelOrder->save()) {
                        $modelOrder->id = $flag;
                        /** @var OrderItems $modelOrderItem */
                        foreach ($modelsOrderItem as $k => $modelOrderItem) {
                            $modelOrderItem->order_id = $modelOrder->id;

                            // Добавляем товары инвоиса на склад
                            $product = Product::findOne(['barcode' => $modelOrderItem->barcode]);
                            $product->quantity -= $modelOrderItem->quantity;

                            if (!$product->save())
                                throw new Exception("Order product is not updated");

                            if (! ($flag = $modelOrderItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        $sum = $itemsCost - $modelOrder->paid_amount;
                        Yii::$app->settings->setBalance($sum);
                    }

                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['order/create']);
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
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws Exception
     */
    public function actionTestCreate()
    {
        $this->layout = 'order';

        if (Yii::$app->request->isAjax) {
                $orderData = Yii::$app->request->post('order');
                $isPrint = Yii::$app->request->post('print');
                $transaction = \Yii::$app->db->beginTransaction();
                $productsToCheck = '';
                try {
                    /** @var Order $order */
                    $order = new Order();

                    $order->number = Order::generateNumber();
                    $order->created_by = Yii::$app->user->identity->getId();
                    $order->customer_id = $orderData['customerId'];
                    $order->cost = $orderData['preTotalSum'];
                    $order->discount_cost = $orderData['discountSum'];
                    $order->total_cost = $orderData['totalSum'];
                    $order->pay_id = $orderData['payMethod'];
                    $order->shift_id = Yii::$app->object->getShiftId();
                    $order->comment = $orderData['comment'];
                    $order->taken_cash = $orderData['takenCash'];

                    if ($order->pay_id == Order::PAID_BY_DEBT && !$orderData['takenCash']) {
                        $order->pay_status = Order::PAY_STATUS_NOT_PAID;
                    } elseif ($order->pay_id == Order::PAID_BY_DEBT && $orderData['takenCash'] > 0) {
                        $order->pay_status = Order::PAY_STATUS_PARTIALLY_PAID;
                    } else {
                        $order->pay_status = Order::PAY_STATUS_PAID;
                    }

                    $order->status = Order::STATUS_SUCCESS;

                    if (!$order->save()) {
                        throw new Exception('Order is not saved');
                    }

                    $order_debt = new OrderDebtHistory();
                    $order_debt->order_id = $order->id;
                    $order_debt->paid_amount = $orderData['takenCash'];
                    if (!$order_debt->save()) {
                        throw new ErrorException( 'Order Debt history is not saved!' );
                    }
                    /** @var OrderItems $order */
                    foreach ($orderData['products'] as $k => $product) {
                        $orderItem = new OrderItems();
                        $orderItem->order_id = $order->id;
                        $orderItem->name = $product['name'];
                        $orderItem->barcode = $product['barcode'];
                        $orderItem->product_id = $product['id'];
                        $orderItem->quantity = $product['quantity'];
                        $orderItem->real_price = $product['priceRetail'];

                        $productStock = Product::findOne(['id' => $product['id']]);
                        $productStock->quantity -= $orderItem->quantity;

                        $productsToCheck .= $productStock->name . ' x ' . $product['quantity'] . ' = ' . $product['priceRetail'] * $product['quantity'] . "\n";

                        if (!$productStock->save())
                            throw new Exception("Order product is not updated");

                        if (! ($flag = $orderItem->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }

                    $productsToCheck .= "Итого: " . $order->total_cost . "\n";
                    Yii::$app->settings->setBalance($order->total_cost);

                    if ($flag) {
                        if ($isPrint == '1' && $order->pay_id != 1) {
                            CashDrawController::printCheck($order);
                        }

                        $transaction->commit();
                        return true;
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }

        }

        return $this->render('test_create');
    }

    public function actionOpenCashDraw() {
        if (Yii::$app->request->isAjax) {
            $connector = new NetworkPrintConnector("192.168.1.87", 9100);
            $printer = new Printer($connector);
            $printer -> pulse(0, 120, 240);
            $printer -> close();
        }

        return false;
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
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionGetProductById($id)
    {
        if (Yii::$app->request->isAjax) {
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
            Yii::$app->settings->setBalance($model->paid_amount);
            return true;
        }

        /** @var Invoice $invoice */
        $order = Order::findOne(['id' => $order_id, 'is_debt' => Order::IS_DEBT_STATUS_YES]);

        return $this->renderAjax('_add-debt', [
            'model' => $model,
            'order' => $order
        ]);
    }

    public function actionSearch($term = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = [];
        if ($term) {
            $searchResult = \common\models\es\Product::find()
                ->query([
                    "multi_match" => [
                        'query' => $term,
                        'fields' => [
                            'name',
                            'barcode'
                        ]
                    ]
                ])
                ->asArray()
                ->all();
            foreach ( $searchResult as $value => $item ) {
                $data[] = [
                    'id' => $item['_source']['id'],
                    'label' => $item['_source']['name'],
                ];
            }
        } else {
            $data = Product::find()
                ->select(['id', 'name as label'])
                ->where(['is_favourite' => Product::IS_FAVOURITE_YES])
                ->asArray()
                ->all();
        }

        return $data;
    }
}
