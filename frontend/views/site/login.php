<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use frontend\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
<img src="/img/ims.png" style="height: 110px; margin-bottom: 30px">

<p class="login-text">Платформа IMS приветствует Вас!</p>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

    <?= $form->field($model, 'password')->label(false)->passwordInput(['placeholder' => 'Введите пароль']) ?>

    <div class="form-group">
        <?= Html::submitButton('Войти', ['class' => 'login-button', 'name' => 'login-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>


