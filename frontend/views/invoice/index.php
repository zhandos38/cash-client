<?php

use common\models\Invoice;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\InvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Накладные';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(['id' => 'invoice-list']);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'number_in',
            [
                'attribute' => 'is_debt',
                'value' => function(Invoice $model) {
                    return $model->getIsDebtStatusLabel();
                },
                'filter' => Invoice::getIsDebtStatus()
            ],
            [
                'attribute'=>'status',
                'options'=>['style'=>'width:50px;'],
                'value' => function(Invoice $model) {
                    if ($model->is_debt && $model->status == Invoice::STATUS_NOT_PAID) {
                        return '<div class="invoice__debt-btn btn btn-primary btn-xs btn-block" data-id="'. $model->id .'"><i  class="glyphicon glyphicon-remove"></i> Не оплачен</div>';
                    } elseif ($model->is_debt && $model->status == Invoice::STATUS_PARTIALLY_PAID) {
                        return '<div class="invoice__debt-btn btn btn-primary btn-xs btn-block" data-id="'. $model->id .'"><i  class="glyphicon glyphicon-remove"></i> Частично оплачен</div>';
                    } else {
                        return '<i class="glyphicon glyphicon-ok"></i> Оплачен';
                    }
                },
                'format' => 'raw',
                'filter' => Invoice::getStatuses()
            ],
            [
                'attribute' => 'cost'
            ],
            [
                'attribute' => 'created_at',
                'value' => function(Invoice $model) {
                    return date('d-m-Y H:i', $model->created_at);
                },
                'filter' => DateRangePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'createTimeRange',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format'=>'Y-m-d'
                            ],
                            'convertFormat'=>true,
                        ]
                    ]),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update} {view} {delete}',
            ],
        ],
    ]); ?>
    <?php Pjax::end();?>

</div>
<?php
Modal::begin([
    'header' => '<h4>Долги</h4>',
    'id' => 'invoice-debt-modal',
    'size' => 'modal-lg'
]);

echo '<div id="invoice-debt-modal__content"></div>';

Modal::end();
?>
<?php
$js =<<<JS
$(document).on("click", '.invoice__debt-btn', function() {
    $('#invoice-debt-modal').modal('show')
    .find('#invoice-debt-modal__content')
    .load('/invoice/add-debt', {'id': $( this ).data('id')});
});
JS;

$this->registerJs($js);
 ?>
