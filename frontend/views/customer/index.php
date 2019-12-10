<?php

use common\models\Customer;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Клиенты';
$this->params['breadcrumbs'][] = $this->title;
?>
<a href="<?= Url::to(['customer/main']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'full_name',
            'phone',
            'address',
            [
                'attribute' => 'birthday_date',
                'value' => function(Customer $model) {
                    return date('d/m/Y', $model->created_at);
                }
            ],
            [
                'attribute' => 'status',
                'value' => function(Customer $model) {
                    return $model->getStatusLabel();
                },
                'filter' => Customer::getStatuses()
            ],
            [
                'attribute' => 'created_at',
                'value' => function(Customer $model) {
                    return date('d/m/Y', $model->created_at);
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}'
            ]
        ],
    ]); ?>


</div>