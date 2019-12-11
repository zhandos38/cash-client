<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $created_by
 * @property int $customer_id
 * @property double $cost
 * @property double $service_cost
 * @property double $discount_cost
 * @property double $total_cost
 * @property int $status
 * @property boolean $is_debt
 * @property int $created_at
 * @property int $updated_at
 *
 * @property DiscountHistory[] $discountHistories
 * @property User $createdBy
 * @property OrderItems[] $orderItems
 * @property int $shift_id [int(11)]
 * @property bool $pay_id [tinyint(3)]
 * @property int $number [bigint(20)]
 * @property string $comment
 * @property int $taken_cash [int(11)]
 * @property bool $pay_status [tinyint(3)]
 * @property bool $is_sent [tinyint(1)]
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_SUCCESS = 0;
    const STATUS_PARTIALLY_RETURNED = 1;
    const STATUS_RETURNED = 2;
    const STATUS_CANCELED = 3;

    const PAY_STATUS_NOT_PAID = 0;
    const PAY_STATUS_PAID = 1;
    const PAY_STATUS_PARTIALLY_PAID = 2;
    const PAY_STATUS_CANCELED = 3;

    const IS_DEBT_STATUS_NO = 0;
    const IS_DEBT_STATUS_YES = 1;

    const PAID_BY_CASH = 0;
    const PAID_BY_CARD = 1;
    const PAID_BY_DEBT = 2;
    const PAID_BY_COMBINE = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className()
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'customer_id', 'status', 'created_at', 'updated_at', 'shift_id', 'number', 'pay_id'], 'integer'],
            [['cost', 'service_cost', 'discount_cost', 'total_cost', 'taken_cash'], 'number'],
            ['is_debt', 'boolean'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            ['comment', 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_by' => 'Создал',
            'customer_id' => 'Клиент',
            'cost' => 'Стоимост',
            'service_cost' => 'Стоимость услуги',
            'discount_cost' => 'Размер скидки',
            'total_cost' => 'Полная стоимость',
            'status' => 'Статус',
            'created_at' => 'Создано в',
            'updated_at' => 'Обновлено в',
            'is_debt' => 'В долг',
            'number' => 'Номер',
            'pay_id' => 'Тип оплаты'
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDiscountHistories()
    {
        return $this->hasMany(DiscountHistory::className(), ['order_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::className(), ['order_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDebtHistories()
    {
        return $this->hasMany(OrderDebtHistory::className(), ['order_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getShift()
    {
        return $this->hasOne(ShiftHistory::className(), ['shift_id' => 'id']);
    }

    /**
     * @return float|int
     */
    public function getItemsCost()
    {
        $cost = 0;

        if (!$this->orderItems) {
            return $cost;
        }

        foreach ($this->orderItems as $item) {
            $cost += ($item->real_price * $item->quantity);
        }

        return $cost;
    }

    /**
     * @return float|int
     */
    public function getDebtHistorySum()
    {
        $sum = 0;

        if (!$this->debtHistories) {
            return $sum;
        }

        foreach ($this->debtHistories as $item) {
            $sum += $item->paid_amount;
        }

        return $sum;
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_SUCCESS => 'Завершен',
            self::STATUS_PARTIALLY_RETURNED => 'Частичный возврат',
            self::STATUS_RETURNED => 'Полный возврат',
            self::STATUS_CANCELED => 'Отмена'
        ];
    }

    public function getStatusLabel()
    {
        return ArrayHelper::getValue(static::getStatuses(), $this->status);
    }

    public static function getStatusLabelById($id)
    {
        return ArrayHelper::getValue(static::getStatuses(), $id);
    }

    public static function getPayStatuses()
    {
        return [
            self::PAY_STATUS_NOT_PAID => 'Не оплачен',
            self::PAY_STATUS_PAID => 'Оплачен',
            self::PAY_STATUS_PARTIALLY_PAID => 'Частично оплачен',
            self::PAY_STATUS_CANCELED => 'Отменен'
        ];
    }

    public function getPayStatusLabel()
    {
        return ArrayHelper::getValue(static::getPayStatuses(), $this->status);
    }

    public static function getPayStatusLabelById($id)
    {
        return ArrayHelper::getValue(static::getPayStatuses(), $id);
    }

    public static function getIsDebtStatuses() {
        return [
            self::IS_DEBT_STATUS_NO => 'Не в долг',
            self::IS_DEBT_STATUS_YES => 'В долг'
        ];
    }

    public static function getPayments()
    {
        return [
            self::PAID_BY_CASH => 'Наличными',
            self::PAID_BY_CARD => 'Без нал.',
            self::PAID_BY_DEBT => 'В долг',
            self::PAID_BY_COMBINE=> 'Комбин.'
        ];
    }

    public function getPaymentLabel()
    {
        return ArrayHelper::getValue(static::getPayments(), $this->status);
    }

    public static function getPaymentLabelById($id)
    {
        return ArrayHelper::getValue(static::getPayments(), $id);
    }

    public function getIsDebtStatusLabel() {
        return ArrayHelper::getValue(static::getIsDebtStatuses(), $this->is_debt);
    }

    public static function generateNumber()
    {
        return time();
    }
}
