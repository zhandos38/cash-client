<?php

namespace common\models;

use Yii;

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
 * @property int $company_id
 *
 * @property Company $company
 * @property Discount $discount
 * @property DiscountHistory[] $discountHistories
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'phone', 'card_number', 'discount_id', 'is_discount_limited', 'discount_value', 'discount_quantity', 'status', 'created_at', 'updated_at', 'company_id'], 'integer'],
            [['address', 'birthday_date'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['discount_id'], 'exist', 'skipOnError' => true, 'targetClass' => Discount::className(), 'targetAttribute' => ['discount_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'full_name' => 'Full Name',
            'phone' => 'Phone',
            'address' => 'Address',
            'birthday_date' => 'Birthday Date',
            'card_number' => 'Card Number',
            'discount_id' => 'Discount ID',
            'is_discount_limited' => 'Is Discount Limited',
            'discount_value' => 'Discount Value',
            'discount_quantity' => 'Discount Quantity',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'company_id' => 'Company ID',
        ];
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
}
