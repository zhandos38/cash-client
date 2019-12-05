<?php

use common\models\Product;
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
/* @var $products Product */
/* @var $suggestProducts Product */

$this->title = 'Создать заказ';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if (!Yii::$app->object->getShiftId())
    throw new \yii\base\UserException('Смена не назначена, пожалуйста начните смену');
?>
<a href="<?= Url::to(['site/index']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="order-create">

    <div class="order-form__wrapper">
        <div class="order-form">

            <div class="product-search">
                <input id="product-search__input" type="text" class="form-control product-search__input">
                <i id="product-search__keyboard" class="product-search__keyboard fas fa-keyboard"></i>
                <i id="product-search__clear" class="product-search__clear fas fa-times"></i>
                <div class="simple-keyboard" style="display: none; position: absolute; top: 34px; margin: 0 auto; z-index: 3;"></div>
            </div>

            <br>

            <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.order-item', // required: css class
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
                    <table class="table table-striped table-responsive">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Наименование</th>
                            <th scope="col">Цена</th>
                            <th scope="col">Кол.</th>
                            <th scope="col">Всего</th>
                        </tr>
                        </thead>
                        <tbody class="container-items">

                        <?php foreach ($modelsOrderItem as $i => $modelOrderItem): ?>

                            <tr class="order-item"><!-- widgetItem -->
                                <th>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                    <div class="clearfix"></div>
                                </th>
                                <?php
                                // necessary for update action.
                                if (! $modelOrderItem->isNewRecord) {
                                    echo Html::activeHiddenInput($modelOrderItem, "[{$i}]id");
                                }
                                ?>
                                <th>
                                    <?= $form->field($modelOrderItem, "[{$i}]product_id")->hiddenInput([
                                        'class' => 'order-item__product-id'
                                    ])
                                        ->label(false) ?>
                                    <?= $form->field($modelOrderItem, "[{$i}]barcode")->hiddenInput([
                                        'maxlength' => true,
                                        'class' => 'order-item__barcode',
                                        'readonly' => true
                                    ])
                                        ->label(false) ?>
                                    <?= $form->field($modelOrderItem, "[{$i}]name")->textInput([
                                        'maxlength' => true,
                                        'class' => 'order-item__input order-item__name',
                                        'data-from-barcode' => 0,
                                        'readonly' => true
                                    ])
                                        ->label(false) ?>
                                </th>
                                <th>
                                    <?= $form->field($modelOrderItem, "[{$i}]real_price")->textInput([
                                        'class' => 'order-item__input order-item__price',
                                        'readOnly' => true
                                    ])
                                        ->label(false) ?>
                                </th>
                                <th>
                                    <?= $form->field($modelOrderItem, "[{$i}]quantity")->textInput([
                                        'maxlength' => true,
                                        'class' => 'order-item__quantity',
                                        'placeholder' => 0
                                    ])
                                        ->label(false) ?>
                                </th>
                                <th>
                                    <div class="order-item__sum"></div>
                                </th>
                            </tr>

                        <?php endforeach; ?>

                        </tbody>
                    </table>
                </div><!-- .panel -->
            </div>

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
                <?= Html::submitButton('Оплатить', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <div id="products-list" class="products__suggest grid"></div>
    </div>

    <div class="numPad" style="display: none; position: absolute; z-index: 3">
        <div class="simple-keyboard-numpad"></div>
        <div class="simple-keyboard-numpadEnd"></div>
    </div>

    <div id="order-pay">
        <div class="order-pay__modal" v-show="modalOpen">

        </div>
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
    $js = <<<JS
let customer_list_content = $('#customer-list_content');
$('.add-item').css('display', 'none');
$(".order__is-debt").on("click", function() {
    let items = $('#dynamic-form').find('.order-item');
    let total_sum = parseFloat($('.order-item__total-cost').val());
    
    if (items.length > 0) {
        $('.order__paid-amount').attr('max', (total_sum.toFixed(2) - 1));
        
        if ( $( this ).is(":checked") ) {
            $('#customer-list').modal('show')
            .find('#customer-list_content')
            .load('customer-list');
            $( this ).prop("checked", false);
        } else {
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

$(document).on('click', '.product-card', function() {
    let product_id = $( this ).data('id');

    $('#dynamic-form').loading({
        message: 'Загрузка'
    });
   
    $.get({
        url: 'get-product-by-id',
        data: {
            id: product_id
        },
        success: function(result) {
            setProduct(result);
            $('#dynamic-form').loading('toggle');
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
                min: 1,
                max: 100,
                step: 0.1,
                decimals: 2,
                boostat: 5,
                maxboostedstep: 10,
                postfix: 'кг'
            }
        } else {
            input_quantity_settings = {
                min: 1,
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
    let items = $( this ).find('.order-item');
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

let masonryOptions = {
      // set itemSelector so .grid-sizer is not used in layout
    itemSelector: '.grid-item',
    // use element for option
    columnWidth: '.grid-sizer',
    percentPosition: true
};

$('.grid').masonry(masonryOptions);

let timer = null;
$('#product-search__input').on('keydown', function() {
    let input = $( this );
    clearTimeout(timer);
    timer = setTimeout(function() {
      let term = input.val();
        if (term != null) {
            getProducts(term);
        }
    }, 1000);
});

$(document).on('click', '#product-search__keyboard', function () {
        $('.simple-keyboard').toggle();
    });
$(document).on('click', '#product-search__clear', function () {
    $('#product-search__input').val('');
    getProducts();
});

getProducts();
function getProducts(term = null) {
    $('#products-list').loading({
        message: 'Загрузка'
    });
    $.get({
        url: '/order/search',
        data: {term: term},
        success: function(result) {
        let products = '';
        let grid = $('.grid');
        grid.masonry('destroy');
        if (result) {
            $('.grid').html('');
            result.forEach(function(value) {
                products += '<div class="product-card grid-item grid-sizer" data-id="' + value['id'] +  '">' +
                    '<div class="product-card__name">' +
                    value['label'] +
                    '</div>' +
                    '<div class="product-card__price">' +
                    <!-- 436 -->
                    '</div>' +
                    '</div>';
            });
            grid.html(products).masonry(masonryOptions);
        } else {
            grid.html('Ничего не найдено!');
        }
            $('#products-list').loading('toggle');
        },
            error: function() {
            console.log('Product search does not work correctly!');
        }
    });
}
JS;

    $this->registerJs($js)
    ?>


</div>
