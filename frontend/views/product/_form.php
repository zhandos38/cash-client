<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price_wholesale')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'price_retail')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'wholesale_value')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'status')->dropDownList(\common\models\Product::getStatuses(), ['prompt' => 'Указать статус']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
