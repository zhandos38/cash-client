<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ObjectType */

$this->title = Yii::t('app', 'Create Object Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Object Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-type-create">

    <?php
    LteBox::begin([
        'type'=>LteConst::TYPE_INFO,
        'isSolid'=>true,
        'boxTools'=>Html::a('Назад <i class="fas fa-arrow-alt-circle-left"></i>', ['index'], ['class' => 'btn btn-danger btn-xs create_button']),
        'tooltip'=>'this tooltip description',
        'title'=>'Добавить тип объекта'
    ])
    ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?php LteBox::end()?>

</div>
