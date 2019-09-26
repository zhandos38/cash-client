<?php


namespace frontend\models;


use common\models\Company;
use common\models\Order;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;

class OrderForm extends Model
{
    public $cost;
    public $total_cost;
    public $is_debt;
    public $status;
    public $discount_cost;
    public $customer_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost', 'total_cost', 'discount_cost', 'customer_id'], 'integer'],
            ['is_debt', 'boolean'],
            [['cost', 'total_cost'], 'required'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cost' => 'Сумма',
            'total_cost' => 'Итого',
            'is_debt' => 'В долг'
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
            $model->total_cost = $this->total_cost;
            $model->is_debt = $this->is_debt;
//            $model->status = Order::STAT
            $model->supplier_id = $this->supplier_id;
            $model->created_by = Yii::$app->user->identity->getId();
            $model->company_id = Yii::$app->user->identity->company_id;
            $model->created_at = time();
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