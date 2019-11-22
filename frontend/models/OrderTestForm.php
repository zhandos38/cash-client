<?php


namespace frontend\models;

/**
 * @property int $id
 * @property int $shift_id
 * @property double $cost
 */

use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\base\UserException;
use common\models\Order;
use common\models\OrderDebtHistory;


class OrderTestForm extends Model
{
    public $cost;
    public $is_debt;
    public $status;
    public $customer_id;
    public $paid_amount;
    public $pay_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'shift_id', 'pay_id'], 'integer'],
            [['cost'], 'number'],
            ['paid_amount', 'default', 'value' => 0],
            ['paid_amount', 'double'],
            ['cost', 'required'],
            ['is_debt', 'boolean']
        ];
    }

    /**
     * Invoice adding.
     *
     * @return bool whether the creating new account was successful and email was sent
     * @throws UserException
     * @throws \yii\db\Exception
     */
    public function save()
    {
        if (!$this->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model = new Order();
            $model->cost = $this->cost;
            $model->created_by = Yii::$app->user->identity->getId();
            $model->number = Order::generateNumber();
            $model->shift_id = Yii::$app->object->getShiftId();

            if ($model->is_debt && !$this->paid_amount) {
                $model->status = Order::STATUS_NOT_PAID;
            } elseif ($model->is_debt && $this->paid_amount > 0) {
                $model->status = Order::STATUS_PARTIALLY_PAID;
            } else {
                $model->status = Order::STATUS_PAID;
            }

            if (!$model->save(false)) {
                throw new ErrorException( 'Invoice not save!' );
            }

            if ($this->paid_amount) {
                $order_debt = new OrderDebtHistory();
                $order_debt->order_id = $model->id;
                $order_debt->paid_amount = $this->paid_amount;
                if (!$order_debt->save()) {
                    throw new ErrorException( 'Invoice History not save!' );
                }
            }

            $transaction->commit();
        } catch (ErrorException $e) {
            $transaction->rollBack();
            throw new UserException($e->getMessage());
        }

        return $model->id;
    }
}