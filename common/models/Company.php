<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property string $name
 * @property int $iin/bin
 * @property string $address_legal
 * @property string $address_actual
 * @property string $ceo
 * @property string $contact_person
 * @property string $phone
 * @property double $balance
 * @property int $manager_id
 * @property int $status
 * @property int $expired_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property BarcodeTemp[] $barcodeTemps
 * @property User $manager
 * @property Customer[] $customers
 * @property Discount[] $discounts
 * @property Invoice[] $invoices
 * @property Order[] $orders
 */
class Company extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
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
            [['manager_id', 'expired_at', 'created_at', 'updated_at'], 'integer'],
            [['balance'], 'number'],
            [['name', 'address_legal', 'address_actual', 'ceo', 'contact_person', 'phone'], 'string', 'max' => 255],
            ['iin', 'string', 'max' => 12],
            [['manager_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['manager_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'iin' => 'Iin/bin',
            'address_legal' => 'Адрес (формальный)',
            'address_actual' => 'Адрес (Фактическии)',
            'ceo' => 'Директор',
            'contact_person' => 'Контакное лицо',
            'phone' => 'Телефон',
            'balance' => 'Баланс',
            'manager_id' => 'Менеджер',
            'expired_at' => 'Дата окончание',
            'created_at' => 'Дата создание',
            'updated_at' => 'Дата обновление',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBarcodeTemps()
    {
        return $this->hasMany(BarcodeTemp::className(), ['company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(User::className(), ['id' => 'manager_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers()
    {
        return $this->hasMany(Customer::className(), ['company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscounts()
    {
        return $this->hasMany(Discount::className(), ['company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['company_id' => 'id']);
    }

    public static function getStatuses() {
        return [
            self::STATUS_INACTIVE => 'Отключен',
            self::STATUS_ACTIVE => 'Включен'
        ];
    }

    /**
     * @return mixed
     */
    public function getStatusLabel()
    {
        return ArrayHelper::getValue(static::getStatuses(), $this->status);
    }

    public function updateBalance($sum, $isAddition = true)
    {
        if ($isAddition) {
            $this->balance += $sum;
        } else {
            $this->balance -= $sum;
        }

        return $this->save(false);
    }
}