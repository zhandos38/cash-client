<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use common\models\User;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'Регистрация работников';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Заполните все поля чтобы зарегистрировать работника:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin([
                'id' => 'form-signup',
            ]); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= $form->field($model, 'full_name') ?>

                <?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
                    'mask' => '+7(999)999-99-99',
                    'clientOptions' => [
                        'removeMaskOnSubmit' => true
                    ]
                ]) ?>

                <?= $form->field($model, 'role')->dropDownList(User::getRoles(), ['prompt' => 'Указать роль']) ?>

                <div class="form-group">
                    <?= Html::submitButton('Зарегистрировать', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
