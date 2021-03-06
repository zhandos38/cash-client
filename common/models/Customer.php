<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property int $full_name
 * @property int $phone
 * @property string $address
 * @property string $birthday_date
 * @property int $card_number
 * @property int $discount_id
 * @property int $is_discount_limited
 * @property int $discount_value
 * @property int $discount_quantity
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Discount $discount
 * @property DiscountHistory[] $discountHistories
 * @property bool $is_sent [tinyint(1)]
 * @property int $exported_at [int(11)]
 * @property string $description
 */
class Customer extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer';
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
            [['card_number', 'discount_id', 'is_discount_limited', 'discount_value', 'discount_quantity', 'status', 'created_at', 'updated_at', 'exported_at'], 'integer'],
            [['address', 'full_name', 'phone'], 'string', 'max' => 255],
            ['description', 'string'],
            [['discount_id'], 'exist', 'skipOnError' => true, 'targetClass' => Discount::class, 'targetAttribute' => ['discount_id' => 'id']],
            ['is_sent', 'boolean'],
            ['birthday_date', 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'full_name' => 'Ф.И.О',
            'phone' => 'Телефон',
            'address' => 'Адрес',
            'birthday_date' => 'Дата рождения',
            'card_number' => 'Номер карты',
            'status' => 'Статус',
            'created_at' => 'Дата добавления',
            'description' => 'Комментарий'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscount()
    {
        return $this->hasOne(Discount::className(), ['id' => 'discount_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscountHistories()
    {
        return $this->hasMany(DiscountHistory::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebts()
    {
        return $this->hasMany(Order::className(), ['customer_id' => 'id'])
                ->andOnCondition(['is_debt' => true]);
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_INACTIVE => 'Отключен',
            self::STATUS_ACTIVE => 'Включен'
        ];
    }

    public function getStatusLabel()
    {
        return ArrayHelper::getValue(static::getStatuses(), $this->status);
    }
}
