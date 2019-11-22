<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ObjectType */

$this->title = Yii::t('app', 'Update Object Type: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Object Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="object-type-update">

    <?php
    LteBox::begin([
        'type'=>LteConst::TYPE_INFO,
        'isSolid'=>true,
        'boxTools'=>Html::a('Назад <i class="fas fa-arrow-alt-circle-left"></i>', ['index'], ['class' => 'btn btn-danger btn-xs create_button']),
        'tooltip'=>'this tooltip description',
        'title'=>'Изменить тип объекта'
    ])
    ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?php LteBox::end()?>

</div>
