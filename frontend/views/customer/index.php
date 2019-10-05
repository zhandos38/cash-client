<?php

use common\models\Customer;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить пользователя', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
