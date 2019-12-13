<?php

use common\models\ShiftHistory;
use common\models\User;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\SupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Смены';
$this->params['breadcrumbs'][] = $this->title;
?>
<a href="<?= Url::to(['/site/index']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="supplier-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'user_id',
                'value' => function(ShiftHistory $model) {
                    return $model->user->full_name;
                },
                'filter' => ArrayHelper::map(User::find()->all(), 'id', 'full_name')
            ],
            [
                'attribute' => 'status',
                'value' => function(ShiftHistory $model) {
                    return $model->getStatusLabel();
                },
                'filter' => ShiftHistory::getStatuses()
            ],
            [
                'attribute' => 'started_at',
                'value' => function(ShiftHistory $model) {
                    return date('m.d.Y H:i', $model->started_at);
                },
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'startTimeRange',
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
                'attribute' => 'closed_at',
                'value' => function(ShiftHistory $model) {
                    return date('m.d.Y H:i', $model->closed_at);
                },
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'closeTimeRange',
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format'=>'Y-m-d'
                        ],
                        'convertFormat'=>true,
                    ]
                ]),
            ],
        ],
    ]); ?>


</div>
