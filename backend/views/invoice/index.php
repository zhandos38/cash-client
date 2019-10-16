<?php

use common\models\Invoice;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use kartik\daterange\DateRangePicker;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Накладные';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index">

    <?php
    LteBox::begin([
        'type'=>LteConst::TYPE_INFO,
        'isSolid'=>true,
        'tooltip'=>'this tooltip description',
        'title'=>'Накладные'
    ])
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'hover' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'company_id',
                'value' => function(Invoice $model) {
                    return $model->company->name;
                },
                'filter' => ArrayHelper::map(\common\models\Company::find()->asArray()->all(), 'id', 'name'),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => [
                        'prompt' => ''
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '100px'
                    ],
                ],
            ],
            'cost',
            [
                'attribute' => 'status',
                'value' => function(Invoice $model) {
                    return $model->getStatusLabel();
                },
                'filter' => Invoice::getStatuses()
            ],
            [
                'attribute' => 'created_at',
                'value' => function(Invoice $model) {
                    return date('m.d.Y H:i', $model->created_at);
                },
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'createTimeRange',
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format'=>'Y-m-d'
                        ],
                        'convertFormat'=>true
                    ]
                ]),
            ]
        ],
    ]); ?>

    <?php LteBox::end()?>

</div>
