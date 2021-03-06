<?php

use common\models\Invoice;
use common\models\Order;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<a href="<?= Url::to(['/site/index']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin([
        'id' => 'order-list'
    ]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'created_by',
                'value' => 'createdBy.full_name'
            ],
            [
                'attribute' => 'customer_name',
                'value' => 'customer.full_name',
                'label' => 'Ф.И.О'
            ],
            [
                'attribute' => 'phone',
                'value' => 'customer.phone',
                'label' => 'Телефон'
            ],
            'cost',
            [
                'attribute' => 'status',
                'value' => function(Order $model) {
                    if ($model->is_debt && $model->status == Order::PAY_STATUS_NOT_PAID) {
                        return '<div class="order__debt-btn btn btn-primary btn-xs btn-block" data-id="'. $model->id .'"><i  class="glyphicon glyphicon-remove"></i> Не оплачен</div>';
                    } elseif ($model->is_debt && $model->status == Order::PAY_STATUS_PARTIALLY_PAID) {
                        return '<div class="order__debt-btn btn btn-primary btn-xs btn-block" data-id="'. $model->id .'"><i  class="glyphicon glyphicon-remove"></i> Частично оплачен</div>';
                    } else {
                        return '<i class="glyphicon glyphicon-ok"></i> Оплачен';
                    }
                },
                'filter' => Order::getStatuses(),
                'format' => 'raw'
            ],
            [
                'attribute' => 'is_debt',
                'value' => function(Order $model) {
                    return $model->getIsDebtStatusLabel();
                },
                'filter' => Order::getIsDebtStatuses()
            ],
            [
                'attribute' => 'created_at',
                'value' => function(Order $model) {
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
                'template' => '{view}{print-order}',
                'buttons'=>[
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['order/view', 'id' => $model->id]), ['class' => 'btn btn-default btn-xs custom_button']);
                    },
                    'print-order' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-print"></span>', ['data-id' => $model->id, 'class' => 'print-order btn btn-default btn-xs custom_button']);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end();?>

</div>
<?php
Modal::begin([
    'header' => '<h4>Долги</h4>',
    'id' => 'order-debt-modal',
    'size' => 'modal-lg'
]);

echo '<div id="order-debt-modal__content"></div>';

Modal::end();
?>
<?php
$js =<<<JS
$(document).on("click", '.order__debt-btn', function() {
    $('#order-debt-modal').modal('show')
    .find('#order-debt-modal__content')
    .load('/order/add-debt', {'id': $( this ).data('id')});
});

$('.print-order').on('click', function() {
    $.post({
        url: '/cash-draw/print-order',
        data: {id: $(this).data('id'), isOpen: false},
        success: function(success) {
            console.log(success);
            console.log('Print order success');
        },
        error: function() {
            console.log('Ошибка!');
        }
    });
});
JS;

$this->registerJs($js);
?>