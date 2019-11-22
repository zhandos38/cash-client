<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;

?>

<a href="<?= Url::to('/site/login') ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="signup-block text-center">
    <img src="/img/ims.png" style="height: 110px; margin-bottom: 20px">
    <p class="signup-text"><?= Yii::t('signup','Для регистрации на платформе IMS заполните форму'); ?></p>

    <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

    <?= $form->field($model, 'username')->label(false)->textInput(['placeholder' => 'Ваш ИИН/БИН будет вашим логином', 'id' => 'username_input']) ?>

    <?= $form->field($model, 'full_name')->label(false)->textInput(['placeholder' => 'Введите ваши Ф.И.О.']) ?>

    <?= $form->field($model, 'email')->label(false)->textInput(['placeholder' => 'Введите ваш E-mail']) ?>

    <?= $form->field($model, 'role')->label(false)->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
        'mask' => '+7(999)999-99-99',
        'clientOptions' => [
            'removeMaskOnSubmit' => true
        ]
    ])->label(false)->textInput(['placeholder' => 'Введите ваш контактный телефон']) ?>

    <div class="form-group">
        <div class="form-password__input">
            <?= $form->field($model, 'password')->label(false)->passwordInput(['class' => 'form-control input-password login__page_input', 'placeholder' => 'Пароль', 'id' => 'password_input']) ?>
            <span class="form-password__input_btn"><i class="fa fa-eye" id="eye" aria-hidden="true"></i></span>
        </div>
    </div>
    <div class="form-group">
        <div class="form-password-repeat__input">
            <?= $form->field($model, 'passwordRepeat')->label(false)->passwordInput(['class' => 'form-control input-password-repeat login__page_input', 'placeholder' => 'Подтвердите пароль', 'id' => 'password_repeat_input']) ?>
            <span class="form-password-repeat__input_btn"><i class="fa fa-eye" id="eye-slash" aria-hidden="true"></i></span>
        </div>
    </div>
    <div class="terms-block">
        <input id="checkbox" type="checkbox" class="signup-checkbox" name="checkbox" onchange="document.getElementById('submit').disabled = !this.checked;">
        <label for="checkbox" class="checkbox-label">Я прочитал и согласен с условиями <a href="#" target="_blank">Пользовательского соглашения</a></label>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('signup','Зарегистрироваться'), ['class' => 'login-button', 'id' => 'submit', 'name' => 'submit', 'disabled' => 'disabled']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <div class="support-info">
        <p>При возникновении вопросов по работе с платформой,<br/> обратитесь в техническую поддержку: +7(777)777-77-77</p>
    </div>
</div>
