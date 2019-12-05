<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Customer */

$this->title = 'Добавить клиента';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<a href="<?= Url::to(['customer/main']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>

<div class="customer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
