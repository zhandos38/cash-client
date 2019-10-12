<?php


namespace frontend\models\forms;


use common\models\Order;
use common\models\OrderDebtHistory;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\VarDumper;

class OrderDebtHistoryForm extends Model
{
    public $order_id;
    public $paid_amount;

    public function rules()
    {
        return [
            ['order_id', 'integer'],
            ['paid_amount', 'number', 'min' => 0.1],
            [['paid_amount', 'order_id'], 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'paid_amount' => 'Сумма'
        ];
    }

    public function save()
    {
        try {
            /** @var Order $order */
            $order = Order::findOne(['id' => $this->order_id]);
            if ($order->itemsCost != $order->debtHistorySum) {
                $invoice_debt_history = new OrderDebtHistory();
                $invoice_debt_history->paid_amount = $this->paid_amount;
                $invoice_debt_history->order_id = $this->order_id;
                $invoice_debt_history->created_at = time();
                $invoice_debt_history->save();
            }

            if ($order->itemsCost == ($order->debtHistorySum + $this->paid_amount)) {
                $order->status = Order::STATUS_PAID;
            } elseif ($order->itemsCost > ($order->debtHistorySum + $this->paid_amount)) {
                $order->status = Order::STATUS_PARTIALLY_PAID;
            };

            $order->save();

        } catch (Exception $e) {
            throw new Exception('Order Debt History error');
        }

        return $this->order_id;
    }
}