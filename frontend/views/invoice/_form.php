<?php

use common\models\Invoice;
use common\models\InvoiceItems;
use common\models\Supplier;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\assets\InvoiceAsset;

InvoiceAsset::register($this);

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $modelInvoice Invoice */
/* @var $modelsInvoiceItem InvoiceItems */
?>

<div class="invoice-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($modelInvoice, 'number_in')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($modelInvoice, 'supplier_id')->widget(\kartik\select2\Select2::className(), [
                'data' => ArrayHelper::map(Supplier::find()->all(), 'id', 'name'),
                'options' => ['placeholder' => 'Выберите поставщика ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>
        </div>
    </div>

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 99, // the maximum times, an element can be added (default 999)
        'min' => 0, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $modelsInvoiceItem[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'name',
            'barcode',
            'quantity',
            'price_in'
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
                <?php foreach ($modelsInvoiceItem as $i => $item): ?>
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
                            if (! $item->isNewRecord) {
                                echo Html::activeHiddenInput($item, "[{$i}]id");
                            }
                            ?>
                            <div class="message-not-found alert alert-danger" role="alert" style="display: none">
                                Товар в базе не найден, пожалуйста введите название или штрихкод товара вручную
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <?= $form->field($item, "[{$i}]barcode")->textInput(['maxlength' => true, 'class' => 'form-control input_barcode']) ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($item, "[{$i}]name")->textInput(['maxlength' => true, 'class' => 'form-control input_name', 'data-from-barcode' => 0]) ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($item, "[{$i}]quantity")->textInput(['maxlength' => true, 'class' => 'form-control input_quantity', 'type' => 'number', 'value' => 0]) ?>
                                </div>
                                <div class="col-sm-2">
                                    <?= $form->field($item, "[{$i}]price_in")->textInput(['maxlength' => true, 'class' => 'form-control input_price', 'type' => 'number']) ?>
                                </div>
                                <?= $form->field($item, "[{$i}]is_new")->hiddenInput(['class' => 'form-control input_is_new'])->label(false) ?>
                                <div class="external-form">
                                    <div class="col-sm-3">
                                        <?= $form->field($item, "[{$i}]percentage_rate")->textInput(['maxlength' => true, 'class' => 'form-control input_percentage-rate', 'type' => 'number', 'step' => 0.01]) ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?= $form->field($item, "[{$i}]price_retail")->textInput(['maxlength' => true, 'class' => 'form-control input_price-retail', 'type' => 'number', 'step' => 0.01]) ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?= $form->field($item, "[{$i}]wholesale_price") ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?= $form->field($item, "[{$i}]wholesale_value") ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?= $form->field($item, "[{$i}]is_partial")->checkbox(['class' => 'input_is_partial', 'value' => 1]) ?>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <a class="barcode-download" href="#">
                                        Распечатать штрих-код
                                        <div class="barcode-img"></div>
                                    </a>
                                </div>
                            </div><!-- .row -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div><!-- .panel -->
    <?php DynamicFormWidget::end(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($modelInvoice, 'is_debt')->checkbox(['class' => 'invoice-form__is-debt', 'value' => 1]) ?>
        </div>
    </div>
    <div class="row">
        <div id="invoice-form__paid-amount-wrapper" class="col-md-6" style="display: none">
            <?= $form->field($modelInvoice, 'paid_amount')->textInput(['class' => 'form-control invoice-form__paid-amount', 'type' => 'number', 'maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($item->isNewRecord ? 'Сохранить' : 'Обновить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js =<<<JS
let dynamic_form = $(".dynamicform_wrapper");
let input_quantity_settings = {
                initval: 1,
                min: 1,
                max: 1000000000,
                step: 1,
                decimals: 0,
                stepinterval: 50,
                maxboostedstep: 10000000,
                postfix: 'шт'
            };
let input_quantity_partial_settings = {
                initval: 1,
                min: 1,
                max: 100,
                step: 0.1,
                decimals: 2,
                boostat: 5,
                maxboostedstep: 10,
                postfix: 'кг'
            };
let input_quantity_percentage_settings = {
                initval: 0,
                min: 0,
                max: 999,
                step: 0.1,
                decimals: 2,
                boostat: 5,
                maxboostedstep: 10,
                postfix: '%'
            };
dynamic_form.on("beforeInsert", function(e, item) {
    console.log("beforeInsert");
});

dynamic_form.on("afterInsert", function(e, item) {
    console.log("afterInsert");
    dynamic_form.find('.input_quantity:last').TouchSpin(input_quantity_settings);
});

dynamic_form.on("beforeDelete", function(e, item) {
    if (! confirm("Вы уверены что хотите удалить товар?")) {
        return false;
    }
    return true;
});

dynamic_form.on("afterDelete", function(e) {
    console.log("Товар удален!");
});

dynamic_form.on("limitReached", function(e, item) {
    alert("Лимит достигнут");
});

$('form').on('beforeSubmit', function() {
    let items = $( this ).find('.item');
    if (items.length > 0) {
        return true;
    } else {
        noProductsAlert();
        return false;
    }
});

$(document).scannerDetection({
    timeBeforeScanTest: 200, // wait for the next character for upto 200ms
	startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
	// endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
	avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
	ignoreIfFocusOn: 'input',
	onComplete: function(barcode, qty){
        $('.add-item').trigger('click');
        checkProduct(barcode);
    }
});

dynamic_form.on('click', '.barcode-download', function() {
    let img = $(this).find('.barcode-img');
    // var url = img.attr('src');
    // var id = img.data('id');
    // $(this).attr("href", url).attr("download", id + ".png");
    
    let barcodeWindow = window.open('', 'PRINT', 'height=400,width=600');
    barcodeWindow.document.write(img.html());
    barcodeWindow.document.close();
    barcodeWindow.focus();
    barcodeWindow.print();
    
    return true;
});

dynamic_form.on('focusout', '.input_barcode', function() {
    let input_barcode = $(this);
    let parent_row = input_barcode.parents('.row');
    let barcode = input_barcode.val();
    if (barcode) {
        checkProduct(barcode, parent_row);
    }
});

dynamic_form.on('focusout', '.input_name', function() {
    let input_name = $(this);
    if (input_name.data("from-barcode") === 0) {
        let product_name = input_name.val();
        let focused_item = input_name.parents('.row');
        let input_barcode = focused_item.find('.input_barcode');
        let input_is_new = focused_item.find('.input_is_new');
        let input_quantity = focused_item.find('.input_quantity');
        
        if (product_name) {
            $.post({
            url: 'get-checked-random-barcode',
            success: function(result) {
                renderBarcode(result, focused_item);
                input_barcode.val(result);
                input_barcode.attr("readonly", true);
                input_name.attr("readonly", true);
                input_is_new.val(1);
                showExternalForm(focused_item);
                input_quantity.focus();
            }
            });
        }
    }
});

$('.invoice-form__is-debt').click(function() {
    
    let items = $('#dynamic-form').find('.item');
    
    if (items.length > 0) {
        let total_sum = 0;
        let total_quantity = 0;
        
        items.each(function() {
            total_sum += parseFloat($( this ).find('.input_price').val());
            total_quantity += parseFloat($( this ).find('.input_quantity').val());
        });
        
        $('#invoice-form__paid-amount-wrapper').toggle('ease');
        
        $('.invoice-form__paid-amount').attr('max', ((total_sum*total_quantity).toFixed(2) - 1));
        
        if ( $( this ).is(":checked") ) {
            $('button[type="button"]').prop('disabled', true);
        } else {
            $('button[type="button"]').prop('disabled', false);
        }
    } else {
        noProductsAlert();
        $( this ).prop("checked", false);
    }
});

dynamic_form.on('click', '.input_is_partial', function() {
    let focused_item = $( this ).parents('.item');
    
    if ( $( this ).is(":checked") ) {
        focused_item.find('.input_quantity').trigger("touchspin.updatesettings", input_quantity_partial_settings);
    } else {
        focused_item.find('.input_quantity').trigger("touchspin.updatesettings", input_quantity_settings);
    }
});

dynamic_form.on('change', '.input_percentage-rate', function() {
    calcRetailPrice($( this ));
});

dynamic_form.on('change', '.input_price', function() {
    calcRetailPrice($( this ));
});

dynamic_form.on('focusout', '.input_price-retail', function() {
    let input_price_retail_val = parseFloat($( this ).val());
    let focused_item = $( this ).parents('.item');
    let input_percentage_rate = focused_item.find('.input_percentage-rate');
    let input_price = focused_item.find('.input_price');
    let input_price_val = parseFloat(input_price.val());
        
    let sum = Math.abs(((input_price_val - input_price_retail_val) / input_price_val) * 100);
    input_percentage_rate.val(sum);
});

function calcRetailPrice(changed_input) {
    let focused_item = changed_input.parents('.item');
    let input_percentage_val = parseFloat(focused_item.find('.input_percentage-rate').val()).toFixed(2);
    let input_price_val = parseFloat(focused_item.find('.input_price').val());
    
    let sum = input_price_val + (( input_price_val / 100) * input_percentage_val );
    sum = sum.toFixed(2);
    focused_item.find('.input_price-retail').val(sum);
}

function checkProduct(barcode, focused_row = null) {
    let body = $('body');
    $.post({
           url: 'check-product',
           data: {barcode: barcode},
           success: function(result) {
               result = $.parseJSON(result);
               
               if (focused_row == null) {
                   focused_row = body.find('.item:last');
               }
               
               let input_barcode = focused_row.find('.input_barcode');
               let input_name = focused_row.find('.input_name');
               let input_is_new = focused_row.find('.input_is_new');
               let input_quantity = focused_row.find('.input_quantity');
               
               input_barcode.val(result['barcode']);
               input_barcode.attr('readonly', true);
                   
               if (result['name'] != null) {
                   input_name.attr('readonly', true);
                   input_name.val(result['name']);
                   input_is_new.val(0);
                   input_quantity.focus();
                   if (result['is_exist']) {
                       showExternalForm(focused_row);
                   }
               } else {
                   focused_row.parent().find('.message-not-found:last').css('display', 'block');
                   input_name.focus();
                   input_name.data("from-barcode", 1);
                   input_is_new.val(1);
                   showExternalForm(focused_row);
               }
               
               renderBarcode(result['barcode'], focused_row);
           },
           error: function() {
               alert('Ошибка');
           }
    });
}

function showExternalForm(focused_item) {
    focused_item.find('.external-form').show();
    focused_item.find('.input_percentage-rate').TouchSpin(input_quantity_percentage_settings);
}

function renderBarcode(barcode, focused_row) {
    barcode_img = focused_row.find('.barcode-img');
    barcode_type = null;
   
    switch (barcode.length) {
        case 8:
            barcode_type = "ean8";
            break;
        case 12:
            barcode_type = "code128";
            break;
        case 13:
            barcode_type = "ean13";
            break;
        case 14:
            barcode_type = "code128";
            break;
    }
  
   barcode_img.barcode(
       barcode,
       barcode_type,
       {
           barHeight:"65",
           barWidth:"2",
           bgColor:"#FFFFFF",
           color:"#000000",
           fontSize:18,
           marginHRI:5,
           moduleSize:"10",
           output:"svg",
           posX:"0",
           posY:"0"
       });
}

function noProductsAlert() {
    alert('Товар отсутвует, пожалуйста добавьте товар или обратитесь к администратору');
}
JS;

$this->registerJs($js);
 ?>