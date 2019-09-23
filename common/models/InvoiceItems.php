<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_items".
 *
 * @property int $id
 * @property int $invoice_id
 * @property string $barcode
 * @property string $name
 * @property int $quantity
 * @property int $price_in
 * @property boolean $is_partial
 *
 * @property Invoice $invoice
 */
class InvoiceItems extends \yii\db\ActiveRecord
{
    public $is_new;
    public $is_exist;
    public $wholesale_value;
    public $wholesale_price;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoice_id', 'quantity', 'price_in', 'is_new', 'is_exist','wholesale_value', 'wholesale_price'], 'integer'],
            [['barcode', 'name'], 'string', 'max' => 255],
            [['barcode', 'quantity', 'price_in'], 'required'],
            ['is_partial', 'boolean'],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_id' => 'Invoice ID',
            'barcode' => 'Штрих код',
            'name' => 'Название',
            'quantity' => 'Количество',
            'price_in' => 'Входная цена',
            'wholesale_price' => 'Оптовая цена',
            'wholesale_value' => 'Оптовая кол-во',
            'is_partial' => 'Частичный товар'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }
}
