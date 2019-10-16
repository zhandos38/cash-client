<?php

use kl83\widgets\AutocompleteDropdown;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\Modal;
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

    <div class="row">
        <div class="col-sm-12">
            <?= AutocompleteDropdown::widget([
                'name' => 'product_name',
                'source' => Url::to(['product/search']),
            ]) ?>
        </div>
    </div>
    <br>

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

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
                            <div class="row order-item">
                                <div class="col-sm-3">
                                    <?= $form->field($modelOrderItem, "[{$i}]barcode")->textInput([
                                        'maxlength' => true,
                                        'class' => 'form-control order-item__barcode',
                                        'readonly' => true
                                    ]) ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($modelOrderItem, "[{$i}]name")->textInput([
                                        'maxlength' => true,
                                        'class' => 'form-control order-item__name',
                                        'data-from-barcode' => 0,
                                        'readonly' => true])
                                    ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($modelOrderItem, "[{$i}]quantity")->textInput([
                                        'maxlength' => true,
                                        'class' => 'order-item__quantity',
                                        'placeholder' => 0
                                    ]) ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($modelOrderItem, "[{$i}]real_price")->textInput([
                                            'class' => 'form-control order-item__price',
                                            'readOnly' => true
                                    ])
                                         ?>
                                    <?= $form->field($modelOrderItem, "[{$i}]product_id")->hiddenInput(['class' => 'order-item__product-id'])->label(false) ?>
                                    <div class="order-item__sum"></div>
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
           <?= $form->field($modelOrder, 'is_debt')->checkbox(['value' => 1, 'class' => 'order__is-debt']) ?>
           <?= $form->field($modelOrder, 'cost')->textInput(['readOnly' => true, 'class' => 'form-control order-item__total-cost']) ?>
       </div>
        <div class="col-md-6">
            <div id="customer" class="panel panel-info" style="display: none">
                <div class="panel-heading">
                    Клиент
                </div>
                <div class="panel-body">
                    <div>
                        Ф.И.О: <span id="customer__name"></span>
                    </div>
                    <div>
                        Телефон: <span id="customer__phone"></span>
                    </div>
                    <div>
                        Адрес: <span id="customer__address"></span>
                    </div>
                    <div>
                        <?= $form->field($modelOrder, 'paid_amount')->textInput(['class' => 'form-control order__paid-amount', 'placeholder' => 'Сумма тг.', 'type' => 'number']) ?>
                        <?= $form->field($modelOrder, 'customer_id')->hiddenInput(['class' => 'order__customer'])->label(false) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
Modal::begin([
    'header' => '<h4>Клиенты</h4>',
    'id' => 'customer-list',
    'size' => 'modal-lg'
]);

echo '<div id="customer-list_content"></div>';

Modal::end();
 ?>
<?php
Modal::begin([
    'header' => '<h4>Добавить клиента</h4>',
    'id' => 'customer-form',
    'size' => 'modal-lg'
]);

echo '<div id="customer-form__content"></div>';

Modal::end();
?>
<?php
$js =<<<JS
let customer_list_content = $('#customer-list_content'); 

$(".order__is-debt").on("click", function() {
    let items = $('#dynamic-form').find('.item');
    let total_sum = parseFloat($('.order-item__total-cost').val());
    
    if (items.length > 0) {
        $('.order__paid-amount').attr('max', (total_sum.toFixed(2) - 1));
        
        if ( $( this ).is(":checked") ) {
            $('#customer-list').modal('show')
            .find('#customer-list_content')
            .load('customer-list');
            $( this ).prop("checked", false);
        } else  {
            let customer_panel = $('#customer');
            let customer_id = $('.order__customer');
            if (customer_panel.css('display') === 'block') {
                customer_panel.css('display', 'none');
                customer_id.val(null);
            }
            $('button[type="button"]').prop('disabled', false);
        }
    } else {
        noProductsAlert();
        $( this ).prop("checked", false);
    }
});

customer_list_content.on('click', '#customer__add-btn', function() {
    $('#customer-form').modal('show')
    .find('#customer-form__content')
    .load('add-customer');
});

customer_list_content.on('click', '.customer-list__item', function() {
    $('.order__customer').val($(this).data('id'));
    let customer_panel = $('#customer');
    let is_debt = $('.order__is-debt');
    $('#customer__name').html($(this).data('name'));
    $('#customer__phone').html($(this).data('phone'));
    $('#customer__address').html($(this).data('address'));
    is_debt.prop('checked', true);
    customer_panel.css('display', 'block');
    $('button[type="button"]').prop('disabled', true);  
});

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
    calculateTotalSum();
});

$(".dynamicform_wrapper").on("limitReached", function(e, item) {
    alert("Лимит достигнут");
});

$(document).on('click', '.ui-menu-item-wrapper', function() {
    let product_id = $('input[name=product_name]').val();
    
    $.post({
        url: 'get-product-by-id',
        data: {
            id: product_id
        },
        success: function(result) {
            setProduct(result);
        },
        error: function() {
            console.log('Ошибка пойска!');
        }
    });
});

$(document).scannerDetection({
    timeBeforeScanTest: 200, // wait for the next character for upto 200ms
	startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
	// endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
	avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
	ignoreIfFocusOn: 'input',
	onComplete: function(barcode, qty){
        $.post({
            url: 'get-product-by-barcode',
            data: {
                barcode: barcode
            },
            success: function(result) {
                setProduct(result);
            },
            error: function() {
                console.log('Ошибка пойска!');
            }
        });
    }
});

function setProduct(product) {
    product = $.parseJSON(product);
    
    console.log(product);
    
    let products = $('.order-item__barcode');
    let flag_is_exist = false;
    
    if (products.length > 0) {
        products.each(function(index) {
            if ($(this).val() === product['barcode']) {
                let item = $(this).closest('.order-item');
                let input_quantity = item.find('.order-item__quantity');
                let quantity_count = input_quantity.val() ? parseInt(input_quantity.val()) + 1 : 1;
                input_quantity.val(quantity_count.toString());
                calculateSum(item);
                flag_is_exist = true;
                return false;
            }
        });
    }
    
    if (!flag_is_exist) {
        $('.add-item').trigger('click');
        let last_item = $('body').find('.order-item:last');
        let last_input_barcode = last_item.find('.order-item__barcode:last');
        let last_input_name = last_item.find('.order-item__name:last');
        let last_input_price = last_item.find('.order-item__price:last');
        let last_input_quantity = last_item.find('.order-item__quantity:last');
        let last_input_sum = last_item.find('.order-item__sum:last');
        let last_input_product_id = last_item.find('.order-item__product-id:last');
        last_input_barcode.val(product['barcode']);
        last_input_name.val(product['name']);
        last_input_price.val(product['price_retail']);
        last_input_quantity.val(1);
        last_input_product_id.val(product['id']);
        last_input_sum.html(product['price_retail']);
        calculateTotalSum();
        
        let input_quantity_settings;
        
        if (product['is_partial'] === '1') {
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
                min: 0,
                max: 1000000000,
                stepinterval: 50,
                maxboostedstep: 10000000,
            }
        }
        $(document).find('.order-item__quantity:last').TouchSpin(input_quantity_settings);   
    }
}

$('#dynamic-form').on('change', '.order-item__quantity', function() {
    let item = $( this ).parents('.order-item');
    calculateSum(item);
});

$('#dynamic-form').on('beforeSubmit', function() {
    let items = $( this ).find('.item');
    if (items.length > 0) {
        return true;
    } else {
        noProductsAlert();
        return false;
    }
});

function calculateSum(item) {
    let item_quantity_val = item.find('.order-item__quantity').val();
    let item_price_val = item.find('.order-item__price').val();
    
    let sum = parseFloat(item_quantity_val) * parseFloat(item_price_val);
    sum = sum.toFixed(2);
    item.find('.order-item__sum').html(sum.toString());
    
    calculateTotalSum();
}

function calculateTotalSum() {
    let total_sum = 0;
    
    $('#dynamic-form').find('.order-item__sum').each(function() {
        total_sum += parseFloat($( this ).html());
    });
    
    $('.order-item__total-cost').val(total_sum);
}

function noProductsAlert() {
    alert('Товар отсутвует, пожалуйста добавьте товар или обратитесь к администратору');
}
JS;

$this->registerJs($js)
?>
