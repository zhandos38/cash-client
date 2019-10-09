<?php


namespace frontend\models\forms;


use common\models\Invoice;
use common\models\InvoiceDebtHistory;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\VarDumper;

class InvoiceDebtHistoryForm extends Model
{
    public $invoice_id;
    public $paid_amount;

    public function rules()
    {
        return [
            ['invoice_id', 'integer'],
            ['paid_amount', 'number', 'min' => 1],
            [['paid_amount', 'invoice_id'], 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'paid_amount' => 'Сумма'
        ];
    }

    public function save()
    {
        try {
            /** @var Invoice $invoice */
            $invoice = Invoice::findOne(['id' => $this->invoice_id]);
            if ($invoice->itemsCost != $invoice->debtHistorySum) {
                $invoice_debt_history = new InvoiceDebtHistory;
                $invoice_debt_history->paid_amount = $this->paid_amount;
                $invoice_debt_history->invoice_id = $this->invoice_id;
                $invoice_debt_history->created_at = time();
                $invoice_debt_history->save();
            } elseif ($invoice->itemsCost == $invoice->debtHistorySum) {
                $invoice->status = Invoice::STATUS_PAID;
                $invoice->save();
            }
        } catch (Exception $e) {
            throw new Exception('Debt History error');
        }

        return $this->invoice_id;
    }
}