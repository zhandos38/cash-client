<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Активация сервера';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <img src="/img/ims.png" style="height: 110px; margin-bottom: 30px">

    <p class="login-text">Вас приветствует платформа IMS!</p>
    <p class="init-text">Это первый запуск сервера IMS.</p>
    <p class="init-text">Вам необходимо осуществить активацию сервера. Для этого Вам необходимо авторизоваться, введя логин и пароль, затем выбрать объект, который Вы хотите активировать.</p><br/>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

    <?= $form->field($model, 'username')->textInput(['value' => $model->username, 'placeholder' => 'Введите ИИН/БИН'])->label(false) ?>

    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Введите пароль'])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Войти', ['class' => 'login-button', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>