<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property int $number_in
 * @property int $is_debt
 * @property int $status
 * @property int $created_by
 * @property int $created_at
 * @property int $supplier_id
 * @property double $cost
 *
 * @property User $createdBy
 * @property Supplier $supplier
 * @property InvoiceItems[] $invoiceItems
 * @property InvoiceDebtHistory[] $debtHistory
 * @property int $is_sent [int(11)]
 */
class Invoice extends \yii\db\ActiveRecord
{
    const STATUS_NOT_PAID = 0;
    const STATUS_PAID = 1;
    const STATUS_PARTIALLY_PAID = 2;

    const STATUS_IS_DEBT_INACTIVE = 0;
    const STATUS_IS_DEBT_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_debt', 'status', 'created_by', 'created_at', 'supplier_id'], 'integer'],
            ['number_in', 'string'],
            ['cost', 'number'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number_in' => 'Номер',
            'is_debt' => 'В долг',
            'status' => 'Статус',
            'created_at' => 'Дата добавление',
            'supplier_id' => 'Поставщик',
            'cost' => 'Сумма'
        ];
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
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceItems()
    {
        return $this->hasMany(InvoiceItems::className(), ['invoice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebtHistory()
    {
        return $this->hasMany(InvoiceDebtHistory::className(), ['invoice_id' => 'id']);
    }

    /**
     * @return float|int
     */
    public function getItemsCost()
    {
        $cost = 0;

        if (!$this->invoiceItems) {
            return $cost;
        }

        foreach ($this->invoiceItems as $item) {
            $cost += ($item->price_in * $item->quantity);
        }

        return $cost;
    }

    /**
     * @return float|int
     */
    public function getDebtHistorySum()
    {
        $sum = 0;

        if (!$this->debtHistory) {
            return $sum;
        }

        foreach ($this->debtHistory as $item) {
            $sum += $item->paid_amount;
        }

        return $sum;
    }

    public static function getIsDebtStatus()
    {
        return [
            self::STATUS_IS_DEBT_INACTIVE => 'Нет',
            self::STATUS_IS_DEBT_ACTIVE => 'Да'
        ];
    }

    public function getIsDebtStatusLabel()
    {
        return ArrayHelper::getValue(static::getIsDebtStatus(), $this->is_debt);
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_NOT_PAID => 'Нет оплочен',
            self::STATUS_PAID => 'Оплочен',
            self::STATUS_PARTIALLY_PAID => 'Оплочен частично'
        ];
    }

    public function getStatusLabel()
    {
        return ArrayHelper::getValue(static::getStatuses(), $this->status);
    }
}