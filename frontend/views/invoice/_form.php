<?php

use common\models\Invoice;
use common\models\InvoiceItems;
use common\models\Supplier;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
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
            <?= $form->field($modelInvoice, 'supplier_id')->dropDownList(ArrayHelper::map(Supplier::find()->all(), 'id', 'name'), ['prompt' => 'Выбрать поставщика']) ?>
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
                                <div class="col-sm-6">
                                    <?= $form->field($item, "[{$i}]barcode")->textInput(['maxlength' => true, 'class' => 'form-control input_barcode']) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($item, "[{$i}]name")->textInput(['maxlength' => true, 'class' => 'form-control input_name', 'data-from-barcode' => 0]) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($item, "[{$i}]quantity")->textInput(['maxlength' => true, 'class' => 'form-control input_quantity', 'type' => 'number']) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($item, "[{$i}]price_in")->textInput(['maxlength' => true, 'class' => 'form-control input_price', 'type' => 'number']) ?>
                                </div>
                                <?= $form->field($item, "[{$i}]is_new")->hiddenInput(['class' => 'form-control input_is_new'])->label(false) ?>
                                <?= $form->field($item, "[{$i}]is_exist")->hiddenInput(['class' => 'form-control input_is_exist'])->label(false) ?>
                                <div class="external-form">
                                    <div class="col-sm-6">
                                        <?= $form->field($item, "[{$i}]wholesale_value") ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($item, "[{$i}]wholesale_price") ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?= $form->field($item, "[{$i}]is_partial")->checkbox(['value' => 1]) ?>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <a class="barcode-download" href="#">
                                        Print
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

$(document).on('click', '.barcode-download', function() {
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

$(document).on('focusout', '.input_barcode', function() {
    let input_barcode = $(this);
    let parent_row = input_barcode.parents('.row');
    let barcode = input_barcode.val();
    if (barcode) {
        checkProduct(barcode, parent_row);
    }
});

$(document).on('focusout', '.input_name', function() {
    let input_name = $(this);
    if (input_name.data("from-barcode") === 0) {
        let product_name = input_name.val();
        let parent_row = input_name.parents('.row');
        let input_barcode = parent_row.find('.input_barcode');
        let input_is_new = parent_row.find('.input_is_new');
        let input_quantity = parent_row.find('.input_quantity');
        let external_form = parent_row.find('.external-form');
        
        if (product_name) {
            $.post({
            url: 'get-checked-random-barcode',
            success: function(result) {
                renderBarcode(result, parent_row);
                input_barcode.val(result);
                input_barcode.attr("readonly", true);
                input_name.attr("readonly", true);
                input_is_new.val(1);
                external_form.show();
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

function checkProduct(barcode, focused_row = null) {
    let body = $('body');
    $.post({
           url: 'check-product',
           data: {barcode: barcode},
           success: function(result) {
               result = $.parseJSON(result);
               
               if (focused_row == null) {
                   focused_row = body.find('.row:last');
               }
               
               let input_barcode = focused_row.find('.input_barcode');
               let input_name = focused_row.find('.input_name');
               let input_is_new = focused_row.find('.input_is_new');
               let input_quantity = focused_row.find('.input_quantity');
               let input_is_exist = focused_row.find('.is_exist');
               let external_form = focused_row.find('.external-form');
               
               input_barcode.val(result['barcode']);
               input_barcode.attr('readonly', true);
                   
               if (result['name'] != null) {
                   input_name.attr('readonly', true);
                   input_name.val(result['name']);
                   input_is_new.val(0);
                   input_quantity.focus();
                   console.log(result);
                   if (result['is_exist']) {
                       input_is_exist.val(1);
                       external_form.show();
                   }
               } else {
                   focused_row.parent().find('.message-not-found:last').css('display', 'block');
                   input_name.focus();
                   input_name.data("from-barcode", 1);
                   input_is_new.val(1);
                   external_form.show();
                   input_is_exist.val(0);
               }
               
               renderBarcode(result['barcode'], focused_row);
           },
           error: function() {
               alert('Ошибка');
           }
    });
}

function renderBarcode(barcode, focused_row) {
    let body = $('body');
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