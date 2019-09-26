<?php

use kl83\widgets\AutocompleteDropdown;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelOrder common\models\Order */
/* @var $modelsOrderItem common\models\OrderItems */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= AutocompleteDropdown::widget([
                'name' => 'product_name',
                'source' => Url::to(['product/source']),
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= AutocompleteDropdown::widget([
                'name' => 'product_barcode',
                'source' => Url::to(['product/source']),
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
        'model' => $modelsOrderItem[0],
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
                            <div class="message-not-found alert alert-danger" role="alert" style="display: none">
                                Товар в базе не найден, пожалуйста введите название или штрихкод товара вручную
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= $form->field($modelOrderItem, "[{$i}]barcode")->textInput(['maxlength' => true, 'class' => 'form-control input_barcode']) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($modelOrderItem, "[{$i}]name")->textInput(['maxlength' => true, 'class' => 'form-control input_name', 'data-from-barcode' => 0]) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($modelOrderItem, "[{$i}]quantity")->textInput(['maxlength' => true, 'class' => 'form-control input_quantity', 'type' => 'number']) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($modelOrderItem, "[{$i}]real_price")->textInput(['maxlength' => true, 'type' => 'number']) ?>
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
        <div class="col-sm-12">
            <?= $form->field($modelOrder, 'cost')->textInput(['readonly' => true]) ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($modelOrder, 'total_cost')->textInput(['readonly' => true]) ?>
        </div>
        <?= $form->field($modelOrder, 'discount_cost')->hiddenInput()->label(false) ?>
        <?= $form->field($modelOrder, 'customer_id')->hiddenInput()->label(false) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
