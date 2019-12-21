<?php

use common\models\Category;
use kartik\color\ColorInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parent_id')->dropDownList(\yii\helpers\ArrayHelper::map(Category::find()->all(), 'id', 'name'), ['prompt' => 'Выберите родителя']) ?>

    <?= $form->field($model, 'color_id')->widget(ColorInput::classname(), [
        'options' => ['placeholder' => 'Выберите цвет', 'class' => 'form-control'],
    ]) ?>

    <?= $form->field($model, 'is_active')->dropDownList(Category::getCategoryLabels(), ['prompt' => 'Выберите статус', 'value' => 1]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
