<?php


namespace frontend\models;


use common\models\Order;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;

class OrderForm extends Model
{
    public $id;
    public $cost;
    public $is_debt;
    public $status;
    public $customer_id;
    public $paid_amount;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id'], 'integer'],
            [['cost', 'paid_amount'], 'number'],
            ['cost', 'required'],
            ['is_debt', 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cost' => 'Итого',
            'is_debt' => 'В долг',
            'paid_amount' => 'Итого оплачено',
            'customer_name' => 'Клиент',
            'phone' => 'Телефон'
        ];
    }

    /**
     * Invoice adding.
     *
     * @return bool whether the creating new account was successful and email was sent
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
            $model->customer_id = $this->customer_id;
            $model->created_by = Yii::$app->user->identity->getId();
            $model->company_id = Yii::$app->user->identity->company_id;
            $model->created_at = time();
            $model->is_debt = $this->is_debt;
            $model->paid_amount = $this->paid_amount;
            if (!$model->save(false)) {
                throw new ErrorException( 'Invoice not save!' );
            }
            $transaction->commit();
        } catch (ErrorException $e) {
            $transaction->rollBack();
        }

        return $model->id;
    }
}