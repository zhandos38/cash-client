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
 * @property int $cost
 * @property int $service_cost
 * @property int $discount_cost
 * @property int $total_cost
 * @property int $status
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
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'customer_id', 'cost', 'service_cost', 'discount_cost', 'total_cost', 'status', 'created_at', 'updated_at', 'company_id'], 'integer'],
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
            'created_by' => 'Created By',
            'customer_id' => 'Customer ID',
            'cost' => 'Cost',
            'service_cost' => 'Service Cost',
            'discount_cost' => 'Discount Cost',
            'total_cost' => 'Total Cost',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'company_id' => 'Company ID',
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
}
