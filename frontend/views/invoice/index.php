<?php

use common\models\Invoice;
use kartik\daterange\DateRangePicker;
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
    <?php Pjax::begin();?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions'=>['class'=>'table table-hover'],
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
                'class'=>'\dixonstarter\togglecolumn\ToggleColumn',
                'options'=>['style'=>'width:50px;'],
                'linkTemplateOn'=>'<i  class="glyphicon glyphicon-ok"></i> {label}',
                'linkTemplateOff'=>'<a class="toggle-column btn btn-default btn-xs btn-block" data-pjax="0" href="{url}"><i  class="glyphicon glyphicon-remove"></i> {label}</a>'
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
