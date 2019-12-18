<?php

use yii\widgets\Pjax;
use common\models\Product;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Склад';
$this->params['breadcrumbs'][] = $this->title;
?>
<a href="<?= Url::to(['product/main']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin(['id' => 'product-index__table']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'barcode',
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
                'attribute' => 'is_favourite',
                'value' => function(Product $model) {
                    if ($model->is_favourite == Product::IS_FAVOURITE_NO) {
                        return '<div class="product__favourite-btn btn btn-primary btn-xs btn-block" data-id="'. $model->id .'"><i  class="glyphicon glyphicon-remove"></i> Не избранный</div>';
                    } elseif ($model->is_favourite == Product::IS_FAVOURITE_YES) {
                        return '<div class="product__favourite-btn btn btn-primary btn-xs btn-block" data-id="'. $model->id .'"><i  class="glyphicon glyphicon-ok"></i> Избранный</div>';
                    }
                    return false;
                },
                'filter' => Product::getIsFavouriteLabels(),
                'format' => 'raw'
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
                'template' => '{update}'
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>


</div>
<?php
$js =<<<JS
$(document).on("click", '.product__favourite-btn', function() {
    let id = $( this ).data('id');
    $.get({
        url: '/product/set-favourite',
        data: {id: id},
        success: function(result) {
            console.log(result);
            $.pjax.reload({container: '#product-index__table'});
        },
        error: function() {
          console.log('Возникла ошибка!');
        }
    });
});
JS;

$this->registerJs($js);
?>
