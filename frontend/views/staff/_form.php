<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
        'mask' => '+7(999)999-99-99',
        'clientOptions' => [
            'removeMaskOnSubmit' => true
        ]
    ]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role')->dropDownList(User::getRoles(), ['prompt' => 'Указать роль']) ?>

    <?= $form->field($model, 'status')->dropDownList(User::getStatuses(), ['prompt' => 'Указать статус']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
