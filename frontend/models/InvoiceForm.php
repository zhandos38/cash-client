<?php


namespace frontend\models;


use common\models\Company;
use common\models\Invoice;
use common\models\InvoiceDebtHistory;
use common\models\Supplier;
use frontend\models\forms\InvoiceDebtHistoryForm;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\Model;
use yii\base\UserException;
use yii\helpers\VarDumper;

class InvoiceForm extends Model
{
    public $id;
    public $number_in;
    public $is_debt;
    public $supplier_id;
    public $company_id;
    public $paid_amount;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_debt', 'supplier_id', 'company_id'], 'integer'],
            ['number_in', 'string'],
            ['paid_amount', 'double'],
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
            'paid_amount' => 'Итого оплачено'
        ];
    }

    /**
     * Invoice adding.
     *
     * @return bool whether the creating new account was successful and email was sent
     * @throws UserException
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
            $model->supplier_id = $this->supplier_id;
            $model->created_by = Yii::$app->user->identity->getId();
            $model->company_id = Yii::$app->user->identity->company_id;
            $model->created_at = time();

            $model->is_debt = $this->is_debt;
            if ($model->is_debt && !$this->paid_amount) {
                $model->status = Invoice::STATUS_NOT_PAID;
            } elseif ($model->is_debt && $this->paid_amount > 0) {
                $model->status = Invoice::STATUS_PARTIALLY_PAID;
            } else {
                $model->status = Invoice::STATUS_PAID;
            }

            if (!$model->save(false)) {
                throw new ErrorException( 'Invoice not save!' );
            }

            if ($this->paid_amount) {
                $invoice_debt = new InvoiceDebtHistory();
                $invoice_debt->invoice_id = $model->id;
                $invoice_debt->paid_amount = $this->paid_amount;
                if (!$invoice_debt->save()) {
                    throw new ErrorException( 'Invoice History not save!' );
                }
            }

            $transaction->commit();
        } catch (ErrorException $e) {
            $transaction->rollBack();
            throw new UserException($e->getMessage());
        }

        return $model->id;
    }
}