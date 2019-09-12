<?php

use common\models\User;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\User */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?php
        LteBox::begin([
            'type'=>LteConst::TYPE_INFO,
            'isSolid'=>true,
            'boxTools'=>Html::a('Добавить <i class="fa fa-plus-circle"></i>', ['create'], ['class' => 'btn btn-success btn-xs create_button']),
            'tooltip'=>'this tooltip description',
            'title'=>'Пользователи'
        ])
     ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'username',
            'full_name',
            'email:email',
            [
                'attribute' => 'phone',
            ],
//            'code_number',
            'role',
            [
                'attribute' => 'company_id',
                'value' => function(User $model) {
                    return $model->company->name;
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function(User $model) {
                    return date('m.d.Y H:i', $model->created_at);
                }
            ],
            [
                'attribute' => 'status',
                'value' => function(User $model) {
                    return $model->getStatusLabel();
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}  {delete}',
            ],
        ],
    ]); ?>

    <?php LteBox::end()?>

</div>