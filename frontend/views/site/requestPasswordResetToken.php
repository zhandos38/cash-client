<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Сброс пароля';
$this->params['breadcrumbs'][] = $this->title;
?>
<a href="<?= Url::to('/site/login') ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="site-login">
    <img src="/img/ims.png" style="height: 110px; margin-bottom: 30px">

    <p class="signup-text"><?= Yii::t('signup','Для сброса пароля введите свой ИИН/БИН'); ?></p>

    <div class="row">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?= $form->field($model, 'username')->label(false)->textInput(['placeholder' => 'Введите ИИН/БИН']) ?>

                <div class="form-group">
                    <?= Html::submitButton('Сбросить пароль', ['class' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
    </div>
    <div class="support-info">
        <p>При возникновении вопросов по работе с платформой,<br/> обратитесь в техническую поддержку: +7(777)777-77-77</p>
    </div>
</div>
