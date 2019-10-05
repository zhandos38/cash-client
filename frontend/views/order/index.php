<?php

use common\models\Order;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать заказ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
                'attribute' => 'customer_id',
                'value' => 'customer.full_name'
            ],
            'cost',
            [
                'attribute' => 'status',
                'value' => function(Order $model) {
                    return $model->getStatusLabel();
                },
                'filter' => Order::getStatuses()
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
