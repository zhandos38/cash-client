<?php

use yii\helpers\Html;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */
/* @var $modelInvoice common\models\Invoice */
/* @var $modelsInvoiceItem \common\models\InvoiceItems */

$this->title = 'Добавить накладную';
$this->params['breadcrumbs'][] = ['label' => 'Накладная', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'modelInvoice' => $modelInvoice,
        'modelsInvoiceItem' => $modelsInvoiceItem
    ]) ?>

</div>
