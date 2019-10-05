<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

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
 * @property double $paid_amount
 * @property int $status
 * @property boolean $is_debt
 * @property int $created_at
 * @property int $updated_at
 * @property int $company_id
 *
 * @property DiscountHistory[] $discountHistories
 * @property Company $company
 * @property User $createdBy
 * @property OrderItems[] $orderItems
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_NOT_PAID = 0;
    const STATUS_PAID = 1;
    const STATUS_PARTIALLY_PAID = 2;
    const STATUS_CANCELED = 3;

    const IS_DEBT_STATUS_NO = 0;
    const IS_DEBT_STATUS_YES = 1;

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
            'class' => TimestampBehavior::className()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'customer_id', 'status', 'created_at', 'updated_at', 'company_id'], 'integer'],
            [['cost', 'service_cost', 'discount_cost', 'total_cost', 'paid_amount'], 'number'],
            ['is_debt', 'boolean'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
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
            'cost' => 'Цена',
            'service_cost' => 'Стоимость услуги',
            'discount_cost' => 'Размер скидки',
            'total_cost' => 'Полная стоимость',
            'status' => 'Статус',
            'created_at' => 'Создано в',
            'updated_at' => 'Обновлено в',
            'paid_amount' => 'Итого оплачено',
            'is_debt' => 'В долг'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscountHistories()
    {
        return $this->hasMany(DiscountHistory::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::className(), ['order_id' => 'id']);
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_NOT_PAID => 'Не оплачен',
            self::STATUS_PAID => 'Оплачен',
            self::STATUS_PARTIALLY_PAID => 'Частично оплачен',
            self::STATUS_CANCELED => 'Отменен'
        ];
    }

    public function getStatusLabel()
    {
        return ArrayHelper::getValue(static::getStatuses(), $this->status);
    }

    public static function getIsDebtStatuses() {
        return [
            self::IS_DEBT_STATUS_NO => 'Не в долг',
            self::IS_DEBT_STATUS_YES => 'В долг'
        ];
    }

    public function getIsDebtStatusLabel() {
        return ArrayHelper::getValue(static::getIsDebtStatuses(), $this->is_debt);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$this->is_debt) {
                $this->status = Order::STATUS_PAID;
            } elseif($this->is_debt && $this->cost > $this->paid_amount) {
                $this->status = Order::STATUS_PARTIALLY_PAID;
            } elseif ($this->cost == $this->paid_amount) {
                $this->status = Order::STATUS_PAID;
            } else {
                $this->status = Order::STATUS_NOT_PAID;
            }
        }
        return true;
    }
}
