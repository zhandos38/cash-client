<?php

use kartik\touchspin\TouchSpin;
use kl83\widgets\AutocompleteDropdown;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\assets\OrderAsset;

OrderAsset::register($this);

/* @var $this yii\web\View */
/* @var $modelOrder common\models\Order */
/* @var $modelsOrderItem common\models\OrderItems */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="order-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="row">
        <div class="col-sm-12">
            <?= AutocompleteDropdown::widget([
                'name' => 'product_name',
                'source' => Url::to(['product/search']),
            ]) ?>
        </div>
    </div>

    <div class="row">
        <?= $form->field($modelOrder, 'customer_id')->hiddenInput()->label(false) ?>
    </div>

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 99, // the maximum times, an element can be added (default 999)
        'min' => 0, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $modelsOrderItem[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'name',
            'barcode',
            'quantity',
            'real_price'
        ],
    ]); ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-envelope"></i> Товары
                <button type="button" class="add-item btn btn-success btn-sm pull-right"><i class="glyphicon glyphicon-plus"></i> Добавить товар</button>
            </h4>
        </div>
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
                <?php foreach ($modelsOrderItem as $i => $modelOrderItem): ?>
                    <div class="item panel panel-default"><!-- widgetItem -->
                        <div class="panel-heading">
                            <h3 class="panel-title pull-left">Товар</h3>
                            <div class="pull-right">
                                <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <?php
                            // necessary for update action.
                            if (! $modelOrderItem->isNewRecord) {
                                echo Html::activeHiddenInput($modelOrderItem, "[{$i}]id");
                            }
                            ?>
                            <div class="row">
                                <div class="col-sm-3">
                                    <?= $form->field($modelOrderItem, "[{$i}]barcode")->textInput(['maxlength' => true, 'class' => 'form-control input_barcode', 'readonly' => true]) ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($modelOrderItem, "[{$i}]name")->textInput(['maxlength' => true, 'class' => 'form-control input_name', 'data-from-barcode' => 0, 'readonly' => true]) ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($modelOrderItem, "[{$i}]quantity")->textInput(['maxlength' => true, 'class' => 'input_quantity']) ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($modelOrderItem, "[{$i}]real_price")->widget(\yii\widgets\MaskedInput::className(), [
                                        'clientOptions' => [
                                            'alias' =>  'decimal',
                                            'groupSeparator' => ',',
                                            'autoGroup' => true,
                                        ],
                                    ]) ?>
                                </div>
                            </div><!-- .row -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div><!-- .panel -->
    <?php DynamicFormWidget::end(); ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js =<<<JS
$(".dynamicform_wrapper").on("beforeInsert", function(e, item) {
    console.log("beforeInsert");
});

$(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    console.log("afterInsert");
});

$(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
    if (! confirm("Вы уверены что хотите удалить товар?")) {
        return false;
    }
    return true;
});

$(".dynamicform_wrapper").on("afterDelete", function(e) {
    console.log("Товар удален!");
});

$(".dynamicform_wrapper").on("limitReached", function(e, item) {
    alert("Лимит достигнут");
});

$(document).on('click', '.ui-menu-item-wrapper', function() {
    let product_id = $('input[name=product_name]').val();
    
    $.post({
        url: 'get-product',
        data: {
            id: product_id
        },
        success: function(result) {
            $('.add-item').trigger('click');
            result = $.parseJSON(result);
            let last_row = $('body').find('.row:last');
            let last_input_barcode = last_row.find('.input_barcode:last');
            let last_input_name = last_row.find('.input_name:last');
            last_input_barcode.val(result['barcode']);
            last_input_name.val(result['name']);
            
            console.log(result['is_partial']);
            let input_quantity_settings;
            
            if (result['is_partial'] == true) {
                input_quantity_settings = {
                    min: 0,
                    max: 100,
                    step: 0.1,
                    decimals: 2,
                    boostat: 5,
                    maxboostedstep: 10,
                    postfix: 'кг'
                }
            } else {
                input_quantity_settings = {
                    min: -1000000000,
                    max: 1000000000,
                    stepinterval: 50,
                    maxboostedstep: 10000000,
                }
            }
            $(document).find('.input_quantity:last').TouchSpin(input_quantity_settings);
        },
        error: function() {
            console.log('Ошибка пойска!');
        }
    });
});
JS;

$this->registerJs($js)
?>
