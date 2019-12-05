<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */
/* @var $modelInvoice common\models\Invoice */
/* @var $modelsInvoiceItem \common\models\InvoiceItems */

$this->title = 'Добавить накладную';
$this->params['breadcrumbs'][] = ['label' => 'Накладная', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<a href="<?= Url::to(['invoice/main']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="invoice-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'modelInvoice' => $modelInvoice,
        'modelsInvoiceItem' => $modelsInvoiceItem
    ]) ?>

</div>
