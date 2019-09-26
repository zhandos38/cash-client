<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Order */
/** @var $modelOrder \common\models\Order*/
/** @var $modelsOrderItem  \common\models\OrderItems*/

$this->title = 'Создать заказ';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'modelOrder' => $modelOrder,
        'modelsOrderItem' => $modelsOrderItem,
    ]) ?>

</div>
