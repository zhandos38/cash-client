<?php


namespace frontend\models\forms;

use common\models\Customer;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;

class CustomerForm extends Model
{
    public $full_name;
    public $phone;
    public $address;
    public $birthday_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address', 'full_name', 'phone', 'birthday_date'], 'string', 'max' => 255],
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
        ];
    }

    /**
     * Invoice adding.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function save()
    {
        if (!$this->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model = new Customer();
            $model->full_name = $this->full_name;
            $model->phone = $this->phone;
            $model->address = $this->address;
            $model->birthday_date = strtotime($this->birthday_date);
            $model->status = Customer::STATUS_ACTIVE;
            if (!$model->save(false)) {
                throw new ErrorException( 'Customer not save!' );
            }
            $transaction->commit();
        } catch (ErrorException $e) {
            $transaction->rollBack();
        }

        return true;
    }
}