<?php

use common\models\Product;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Склад';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить товар', ['invoice/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin() ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'barcode',
//            'quantity',
//            'price_wholesale',
            //'price_retail',
            //'wholesale_value',
            [
                'attribute' => 'is_partial',
                'value' => function(Product $model) {
                    return $model->getBooleanStatus();
                },
                'filter' => Product::getBooleanStatuses()
            ],
            [
                'attribute' => 'status',
                'value' => function(Product $model) {
                    return $model->getStatusLabel();
                },
                'filter' => Product::getStatuses()
            ],
            [
                'attribute' => 'quantity',
                'filter' => false
            ],
            [
                'attribute' => 'price_retail',
                'filter' => false
            ],
            [
                'attribute' => 'price_wholesale',
                'filter' => false
            ],
            [
                'attribute' => 'updated_at',
                'value' => function(Product $model) {
                    return date('d-m-Y H:i', $model->updated_at);
                },
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updateTimeRange',
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
                'template'=>'{update}',
            ],
        ],
    ]); ?>

    <?php Pjax::end() ?>

</div>
