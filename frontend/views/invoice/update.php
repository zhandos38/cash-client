<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $modelInvoice common\models\Invoice */
/* @var $modelsInvoiceItem common\models\InvoiceItems */

$this->title = 'Изменить накладную: ' . $modelInvoice->number_in;
$this->params['breadcrumbs'][] = ['label' => 'Накладная', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $modelInvoice->number_in, 'url' => ['view', 'id' => $modelInvoice->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<a href="<?= Url::to(['invoice/index']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="invoice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'modelInvoice' => $modelInvoice,
        'modelsInvoiceItem' => $modelsInvoiceItem
    ]) ?>

</div>
