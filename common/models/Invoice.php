<?php

namespace common\models;

use dixonstarter\togglecolumn\ToggleActionInterface;
use Yii;
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
 * @property int $company_id
 *
 * @property Company $company
 * @property User $createdBy
 * @property Supplier $supplier
 * @property InvoiceItems[] $invoiceItems
 */
class Invoice extends \yii\db\ActiveRecord implements ToggleActionInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

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
            [['is_debt', 'status', 'created_by', 'created_at', 'supplier_id', 'company_id'], 'integer'],
            ['number_in', 'string'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
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
            'company_id' => 'Компание',
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

    use \dixonstarter\togglecolumn\ToggleActionTrait;
    public function getToggleItems()
    {
        return  [
            'on' => ['value' => 1, 'label'=>'Оплачен'],
            'off' => ['value' => 0, 'label'=>'Оплатить'],
        ];
    }
}