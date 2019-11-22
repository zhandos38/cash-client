<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "order_items".
 *
 * @property int $id
 * @property int $product_id
 * @property int $order_id
 * @property string $name
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
 * @property bool $status [tinyint(3)]
 */
class OrderItems extends \yii\db\ActiveRecord
{
    const STATUS_CLOSED = 0;
    const STATUS_PARTIAL_RETURNED = 1;
    CONST STATUS_CANCELED = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_items';
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
            [['product_id', 'order_id', 'created_at', 'updated_at', 'took_at', 'finished_at', 'status'], 'integer'],
            [['quantity', 'real_price'], 'number'],
            [['name', 'barcode'], 'string', 'max' => 255],
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
            'order_id' => 'Order ID',
            'name' => 'Название',
            'barcode' => 'Штрих код',
            'quantity' => 'Количество',
            'real_price' => 'Цена',
            'created_at' => 'Дата добавления',
            'updated_at' => 'Дата обновление',
            'took_at' => 'Было в',
            'finished_at' => 'Закончена в',
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
