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
        <div class="col-sm-12">
            <?= $form->field($modelInvoice, 'is_debt')->checkbox(['value' => 1]) ?>
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
                <?php foreach ($modelsInvoiceItem as $i => $modelInvoice): ?>
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
                            if (! $modelInvoice->isNewRecord) {
                                echo Html::activeHiddenInput($modelInvoice, "[{$i}]id");
                            }
                            ?>
                            <div class="message-not-found alert alert-danger" role="alert" style="display: none">
                                Товар в базе не найден, пожалуйста введите название или штрихкод товара вручную
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= $form->field($modelInvoice, "[{$i}]barcode")->textInput(['maxlength' => true, 'class' => 'form-control input_barcode']) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($modelInvoice, "[{$i}]name")->textInput(['maxlength' => true, 'class' => 'form-control input_name', 'data-from-barcode' => 0]) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($modelInvoice, "[{$i}]quantity")->textInput(['maxlength' => true, 'class' => 'form-control input_quantity', 'type' => 'number']) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($modelInvoice, "[{$i}]price_in")->textInput(['maxlength' => true, 'type' => 'number']) ?>
                                </div>
                                <?= $form->field($modelInvoice, "[{$i}]is_new")->hiddenInput(['class' => 'form-control input_is_new'])->label(false) ?>
                                <?= $form->field($modelInvoice, "[{$i}]is_exist")->hiddenInput(['class' => 'form-control input_is_exist'])->label(false) ?>
                                <div class="external-form">
                                    <div class="col-sm-6">
                                        <?= $form->field($modelInvoice, "[{$i}]wholesale_value") ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($modelInvoice, "[{$i}]wholesale_price") ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?= $form->field($modelInvoice, "[{$i}]is_partial")->checkbox(['value' => 1]) ?>
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

    <div class="form-group">
        <?= Html::submitButton($modelInvoice->isNewRecord ? 'Сохранить' : 'Обновить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js =<<<JS
$( document ).ready(function() {
    let button = $('button');
    $('input').prop('disabled', true);
    $('select').prop('disabled', true);
    button.prop('disabled', true);
    button.css('display', 'none');
    
    renderAllBarcode();
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
});

$(".dynamicform_wrapper").on("limitReached", function(e, item) {
    alert("Лимит достигнут");
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

function renderAllBarcode() {
    let items = $('.container-items').find('.row');
    items.each(function() {
        let barcode = $( this ).find('.input_barcode').val();
        let barcode_img = $( this ).find('.barcode-img');
        let barcode_type = null;
   
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
        });
}
JS;

$this->registerJs($js);
 ?>