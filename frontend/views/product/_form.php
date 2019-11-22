<?php

use common\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <a href="<?= Url::to(['product/main']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price_wholesale')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'price_retail')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'wholesale_value')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'quantity')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'status')->dropDownList(Product::getStatuses(), ['prompt' => 'Указать статус']) ?>

    <?= $form->field($model, 'is_favourite')->checkbox(['value' => Product::IS_FAVOURITE_YES]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
