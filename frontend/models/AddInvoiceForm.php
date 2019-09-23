<?php


namespace frontend\models;


use common\models\Company;
use common\models\Invoice;
use common\models\Supplier;
use common\models\User;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\helpers\VarDumper;

class AddInvoiceForm extends Model
{
    public $id;
    public $number_in;
    public $is_debt;
    public $supplier_id;
    public $company_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_debt', 'supplier_id', 'company_id'], 'integer'],
            ['number_in', 'string'],
            [['supplier_id', 'number_in'], 'required'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'number_in' => 'Номер',
            'is_debt' => 'В долг',
            'status' => 'Статус',
            'created_at' => 'Дата добавления',
            'supplier_id' => 'Поставщик',
            'company_id' => 'Компания',
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
            $model = new Invoice();
            $model->number_in = $this->number_in;
            $model->is_debt = $this->is_debt;
            $model->status = Invoice::STATUS_ACTIVE;
            $model->supplier_id = $this->supplier_id;
            $model->created_by = Yii::$app->user->identity->getId();
            $model->company_id = Yii::$app->user->identity->company_id;
            $model->created_at = time();
            if (!$model->save(false)) {
                throw new ErrorException( 'Invoice not save!' );
            }
            $transaction->commit();
        } catch (ErrorException $e) {
            $transaction->rollBack();
        }

        return $model->id;
    }
}