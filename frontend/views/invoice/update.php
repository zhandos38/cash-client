<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $modelInvoice common\models\Invoice */
/* @var $modelsInvoiceItem common\models\InvoiceItems */

$this->title = 'Update Invoice: ' . $modelInvoice->id;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $modelInvoice->id, 'url' => ['view', 'id' => $modelInvoice->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="invoice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'modelInvoice' => $modelInvoice,
        'modelsInvoiceItem' => $modelsInvoiceItem
    ]) ?>

</div>
