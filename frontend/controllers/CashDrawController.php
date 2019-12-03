<?php


namespace frontend\controllers;


use common\models\Order;
use common\models\OrderItems;
use common\models\ShiftHistory;
use common\models\ShiftTransactions;
use frontend\models\BillSearch;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Yii;
use yii\base\UserException;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use yii\db\Transaction;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\Response;

class CashDrawController extends Controller
{
    /* https://poshelp.robotill.com/OpenDrawerCodes.aspx */

    public function actionIndex()
    {
        $this->layout = 'cash-draw';

        $shift = ShiftHistory::findOne(['id' => Yii::$app->object->getShiftId()]);
        $transactions = ShiftTransactions::find()->all();
        return $this->render('index', [
            'shift' => $shift,
            'transactions' => $transactions
        ]);
    }

    public function actionOrders()
    {
        $this->layout = 'cash-draw';

        $searchModel = new BillSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('orders', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionGetData()
    {
        if (Yii::$app->request->isAjax) {
            $shift_id = Yii::$app->object->getShiftId();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $shift = ShiftHistory::find()->where(['id' => $shift_id])->asArray()->all();
            $transactions = ShiftTransactions::find()->where(['id' => $shift_id])->asArray()->all();
            return array_merge($shift, $transactions);
        }

        return false;
    }

    public function actionAddTransaction()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $transaction = new ShiftTransactions();
            $transaction->sum = (float) $data['value'];
            $transaction->type_id = (int) $data['type'];
            $transaction->comment = $data['comment'];
            $transaction->user_id = Yii::$app->user->identity->getId();
            $transaction->shift_id = Yii::$app->object->getShiftId();
            $transaction->validate();
            return $transaction->save();
        }

        return false;
    }

    public function actionGetTransactions()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = [];
            $transactions = ShiftTransactions::find()->where(['shift_id' => Yii::$app->object->getShiftId()])->all();
            foreach ($transactions as $transaction) {
                $data[] = [
                    'sum' => $transaction->sum,
                    'created_at' => date('d/m/Y H:i', $transaction->created_at),
                    'type' => $transaction->type_id == 0 ? 'Внесение' : 'Изъятие',
                    'comment' => $transaction->comment,
                    'user' => $transaction->user->full_name
                ];
            }
            return $data;
        }

        return false;
    }

    public function actionGetShift()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $shift_id = Yii::$app->object->getShiftId();
            $shift = ShiftHistory::find()->where(['id' => $shift_id])->one();
            $transactions = ShiftTransactions::find()->where(['shift_id' => $shift_id, 'type_id' => ShiftTransactions::TYPE_INSERT])->all();
            $insertedSum = 0;
            foreach ($transactions as $transaction) {
                $insertedSum += $transaction->sum;
            }

            $data = [
                'user' => $shift->user->full_name,
                'created_at' => date('d/m/Y H:i', $shift->started_at),
                'balance_at_start' => null,
                'inserted_money' => $insertedSum,
                'current_balance' => null
            ];

            return $data;
        }

        return false;
    }

    public function actionCloseShift()
    {
        if (Yii::$app->request->isAjax) {
            $this->redirect(['staff/close-shift']);
        }

        return false;
    }

    public function actionGetOrders()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $orders = Order::find()->where(['shift_id' => Yii::$app->object->getShiftId()])->all();
            $data = [];
            foreach ($orders as $order) {
                $data[] = [
                    'id' => $order->id,
                    'number' => $order->number,
                    'status' => Order::getStatusLabelById($order->status),
                    'sum' => $order->total_cost,
                    'pay' => Order::getPaymentLabelById($order->pay_id),
                    'items' => $order->orderItems
                ];
            }

            return $data;
        }

        return false;
    }

    public function actionReturnOrder()
    {
        if (Yii::$app->request->isAjax) {
            $requestData = Yii::$app->request->post();
            $order = Order::findOne(['id' => $requestData['id']]);
            $productsId = [];
            $products = [];
            foreach ($requestData['products'] as $product) {
                $productsId[] = $product['id'];
                $products[] = $product;
            }

            $toBeReturnProducts = OrderItems::find()
                ->where(['in', 'id', $productsId])
                ->orderBy([new \yii\db\Expression('FIELD (id, ' . implode(',', array_reverse(array_keys($productsId))) . ')')])
                ->all();

            foreach ($toBeReturnProducts as $k => $toBeReturnProduct) {
                $toBeReturnProduct->quantity -= (int)$products[$k]['quantity'];
                $toBeReturnProduct->save();
            }

            if ((int)$requestData['isReturnTotal']) {
                $order->status = Order::STATUS_RETURNED;
            } else {
                $order->status = Order::STATUS_PARTIALLY_RETURNED;
            }

            return $order->save();
        }

        return false;
    }

    public function actionCancelOrder()
    {
        if (Yii::$app->request->isAjax) {
            $requestId = Yii::$app->request->post('id');
            $order = Order::findOne(['id' => $requestId]);
            $order->status = Order::STATUS_CANCELED;
            return $order->save();
        }

        return false;
    }

    public function actionPrintOrder()
    {
        if (Yii::$app->request->isAjax) {
            $requestId = Yii::$app->request->post('id');
            $order = Order::findOne(['id' => $requestId]);
            self::printCheck($order);
        }

        return false;
    }

    public static function printCheck($order) {
        $orderTotalCost = 0;
        $profile = CapabilityProfile::load("CT-S651");
//                            $connector = new FilePrintConnector("//DESKTOP-MKRQL8M/RP80Printer");
        $connector = new NetworkPrintConnector("192.168.1.87", 9100);
        $printer = new Printer($connector, $profile);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text(str_pad('Магазин', 20, '=', STR_PAD_BOTH) . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setEmphasis(true);
        $printer->setBarcodeHeight(48);
        $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
        $printer -> text('Чек #' . $order->number . "\n\n");
        $printer->barcode($order->number);
        $printer -> feed(1);
        $printer->setEmphasis(false);
        foreach ($order->orderItems as $k => $item) {
            $k++;
            $itemTotalCost = $item->real_price*$item->quantity;
            $orderTotalCost += $itemTotalCost;
            $printer -> text("$k) $item->name | $item->real_price тг. х $item->quantity = $orderTotalCost \n");
        }
        $printer -> feed(2);
        $printer -> text('Итого: ' . $orderTotalCost . ' тг.');
        $printer -> feed(2);
        $printer -> cut();
        $printer -> pulse(0, 120, 240);
        $printer -> close();
    }
}