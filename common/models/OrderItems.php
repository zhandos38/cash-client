<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_items".
 *
 * @property int $id
 * @property int $product_id
 * @property int $order_id
 * @property string $product_name
 * @property string $barcode
 * @property int $quantity
 * @property int $real_price
 * @property int $created_at
 * @property int $updated_at
 * @property int $took_at
 * @property int $finished_at
 *
 * @property Order $order
 * @property Product $product
 */
class OrderItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'order_id', 'quantity', 'real_price', 'created_at', 'updated_at', 'took_at', 'finished_at'], 'integer'],
            [['product_name', 'barcode'], 'string', 'max' => 255],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'order_id' => 'Order ID',
            'product_name' => 'Product Name',
            'barcode' => 'Barcode',
            'quantity' => 'Quantity',
            'real_price' => 'Real Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'took_at' => 'Took At',
            'finished_at' => 'Finished At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}
