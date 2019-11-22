<?php

use common\models\Invoice;
use common\models\InvoiceDebtHistory;
use frontend\models\forms\InvoiceDebtHistoryForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var InvoiceDebtHistory $debtList */
/** @var InvoiceDebtHistory $item */
/** @var Invoice $invoice */
/** @var InvoiceDebtHistoryForm $model */
/* @var $this yii\web\View */

$totalCost = $invoice->itemsCost;
$totalPaid = $invoice->debtHistorySum;
 ?>
<div class="add-debt">
    <div class="row">
        <div class="col-md-6">
            <div>Сумма: <span class="add-debt__total-cost"><?= $totalCost ?></span></div>
            <div>Оплачено: <span class="add-debt__total-paid"><?= $totalPaid ?></span></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin([
                'id' => $model->formName()
            ]) ?>

                <?= $form->field($model, 'invoice_id')->hiddenInput(['value' => $invoice->id])->label(false) ?>

                <?= $form->field($model, 'paid_amount')->textInput(['type' => 'number', 'class' => 'form-control add-debt__paid-amount', 'placeholder' => 'Сумма', 'value' => ($totalCost-$totalPaid), 'max' => ($totalCost-$totalPaid)]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if (!empty($invoice->debtHistory)): ?>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Сумма</th>
                    <th scope="col">Дата</th>
                </tr>
                </thead>
                <tbody>
            <?php foreach ($invoice->debtHistory as $item): ?>
                <tr>
                    <th><?= $item->paid_amount ?></th>
                    <th><?= date('d-m-Y H:i', $item->created_at) ?></th>
                </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php

$js =<<<JS
$('form#{$model->formName()}').on('beforeSubmit', function(event) {
    let data = $( this ).serialize();
    let parent_modal = $( this ).parents('.modal');
    
    $.post({
        url: '/invoice/add-debt',
        data: data,
        success: function(result) {
            $.pjax.reload({container: '#invoice-list'});
            parent_modal.modal('hide');
        },
        error: function() {
            console.log('error');
        }
    });
    
    return false;
});
JS;

$this->registerJs($js);
 ?>