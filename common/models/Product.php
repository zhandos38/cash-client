<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $barcode
 * @property string $name
 * @property int $quantity
 * @property int $price_wholesale
 * @property int $price_retail
 * @property int $wholesale_value
 * @property int $is_partial
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property OrderItems[] $orderItems
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quantity', 'price_wholesale', 'price_retail', 'wholesale_value', 'is_partial', 'status', 'created_at', 'updated_at'], 'integer'],
            [['barcode', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'barcode' => 'Barcode',
            'name' => 'Name',
            'quantity' => 'Quantity',
            'price_wholesale' => 'Price Wholesale',
            'price_retail' => 'Price Retail',
            'wholesale_value' => 'Wholesale Value',
            'is_partial' => 'Is Partial',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::className(), ['product_id' => 'id']);
    }
}
