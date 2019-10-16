<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="customer-index">

    <p>
        <button id="customer__add-btn" class="btn btn-success">
            Добавить пользователя
        </button>
    </p>

    <div class="customer-search">

        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'id' => 'customer-search-form'
            ]
        ]); ?>

        <?= $form->field($model, 'full_name') ?>

        <?= $form->field($model, 'phone')->widget(MaskedInput::className(), [
            'mask' => '+7(999)999-99-99',
            'clientOptions' => [
                'removeMaskOnSubmit' => true
            ]
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Искать', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('Сбросить', ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <div id="customer-list">
        <div id="customer-list__alert" class="alert alert-danger" style="display: none">
            <strong>Не найдено</strong> никаких записей
        </div>
        <div id="customer-list__list" class="list-group" style="display: none">
        </div>
    </div>

</div>
<?php
$js =<<<JS
$('#customer-search-form').on('beforeSubmit', function() {
   let data = $(this).serialize();
    $.post({
        url: 'customer-list',
        data: data,
        success: function(result) {
            result = $.parseJSON(result);
            console.log(result);
            let list = $('#customer-list__list');
            let alert = $('#customer-list__alert');
            let items = '';
            if (!$.isEmptyObject(result)) {
                $.each(result, function(index, item) {
                    items += '<a href="javascript:void(0)" class="list-group-item list-group-item-action customer-list__item" data-id="' + 
                    item['id'] + '" data-name="' + 
                    item['full_name'] + '" data-phone="' + 
                    item['phone'] + '" data-address="' +
                    item['address'] + '">' + 
                    item['full_name'] + ' | ' + item['phone'] + ' | ' + item['address'] + '</a>';
                });
                list.html(items);
                list.css('display', 'block');
                alert.css('display', 'none');
            } else {
                alert.css('display', 'block');
            }
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
