<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\Customer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'customer-create-form'
        ]
    ]); ?>

    <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
        'mask' => '+7(999)999-99-99',
        'clientOptions' => [
            'removeMaskOnSubmit' => true
        ]
    ]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'birthday_date')->widget(DatePicker::className(), [
        'options' => [
            'value' => '01/01/1980'
        ],
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'mm/dd/yyyy'
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$js =<<<JS
$('#customer-create-form').on('beforeSubmit', function() {
   let data = $(this).serialize();
   let close_btn = $('#customer-form').find('.close');
    $.post({
        url: 'add-customer',
        data: data,
        success: function(result){
            close_btn.trigger('click');
        },
        error: function(){
            alert('Error!');
        }
    });
    return false;
});
JS;

$this->registerJs($js);
?>
