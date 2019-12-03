<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <img src="/img/ims.png" style="height: 110px; margin-bottom: 30px">

    <p class="login-text" style="margin-bottom: 10px">Платформа IMS приветствует Вас!</p>

    <div class="init-text">
        <p>Это первый запуск сервера IMS.<br/> Вам необходимо осуществить привязку сервера к платформе IMS. Для этого Вам необходимо ввести активационный ключ в соответствующее поле и нажать на кнопку "Привязать".</p>
    </div>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

    <?= $form->field($model, 'token')->label(false)->textInput(['placeholder' => 'Введите активационный ключ']) ?>

    <div class="form-group">
        <?= Html::submitButton('Привязать', ['class' => 'login-button', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>