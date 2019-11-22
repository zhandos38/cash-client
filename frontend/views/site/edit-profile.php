<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'Мой профиль';
$this->params['breadcrumbs'][] = $this->title;
?>

<a href="<?= Url::to('/site/index') ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>

<div class="signup-block text-center">
    <img src="/img/ims.png" style="height: 110px; margin-bottom: 30px">
        <?php $form = ActiveForm::begin(['id' => 'form-profile']); ?>
        <div class="tt-item">
            <p class="signup-text"><?= Yii::t('signup','Мой профиль'); ?></p>
            <div class="form-default text-left">
                <form id="contactform" method="post" novalidate="novalidate">
                    <div class="form-group">
                        <?= $form->field($user, 'username')->textInput(['readOnly' => true]) ?>
                    </div>
                    <div class="form-group">
                        <?= $form->field($user, 'email')->textInput(['readOnly' => true]) ?>
                    </div>
                    <div class="form-group">
                        <?= $form->field($user, 'full_name')->textInput(['readOnly' => true]) ?>
                    </div>
                    <div class="form-group">
                        <?= $form->field($user, 'phone')->widget(MaskedInput::className(), [
                            'mask' => '+7(999)999-99-99',
                            'clientOptions' => [
                                'removeMaskOnSubmit' => true
                            ]
                        ])->textInput(['readOnly' => true]) ?>
                    </div>
                    <div class="form-group">
                        <div class="form-password__input">
                            <?= $form->field($model, 'password')->passwordInput(['class' => 'form-control input-password login__page_input', 'placeholder' => 'Введите новый пароль']) ?>
                            <span class="form-password__input_btn" style="top: 23px"><i class="fa fa-eye" id="eye" aria-hidden="true"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app','Сохранить'), ['class' => 'login-button', 'name' => 'profile-button']) ?>
                    </div>
                </form>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
    <div class="support-info">
        <p>Для изменения контактных данных отправьте письмо с заявкой на адрес info@ims-tmt.kz</p>
    </div>
</div>