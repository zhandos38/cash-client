<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Supplier */

$this->title = 'Изменить Поставщика: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<a href="<?= Url::to(['supplier/index']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="supplier-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
