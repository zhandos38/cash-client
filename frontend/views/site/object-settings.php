<?php

use kartik\date\DatePicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'Настройки объекта';

?>
<a href="<?= Url::to('/site/index') ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>

<div class="signup-block text-center" style="width: 80%; margin-bottom: 60px;">
    <img src="/img/ims.png" style="height: 110px; margin-bottom: 30px">
    <?php $form = ActiveForm::begin(['id' => 'form-profile']); ?>
    <div class="tt-item">
        <p class="signup-text"><?= Yii::t('signup','Данные объекта'); ?></p>
        <div class="form-default">
            <form id="contactform" method="post" novalidate="novalidate">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($settingForm, 'name')->textInput(['readonly'=> true]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Тип объекта</label>
                            <input type="text" class="form-control" readonly value="<?= $settingForm->getTypeLabel() ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($settingForm, 'address')->textInput() ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($settingForm, 'phone')->widget(MaskedInput::className(), [
                            'mask' => '+7(999)999-99-99',
                            'clientOptions' => [
                                'removeMaskOnSubmit' => true
                            ]
                        ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Статус объекта</label>
                            <input class="form-control" readonly value="<?= $settingForm->getActivateLabel() ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Дата создания объекта</label>
                            <input type="text" class="form-control" readonly value="<?= Yii::$app->formatter->asDate($settingForm->created_at, 'php:d.m.Y') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Дата окончания лицензии</label>
                            <input type="text" class="form-control" readonly value="<?= Yii::$app->formatter->asDate($settingForm->expired_at, 'php:d.m.Y') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Данные отправляются в ООФД</label>
                            <select class="form-control">
                                <option>Нет</option>
                                <option>Да</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Ключ к ООФД</label>
                            <input type="text" class="form-control" readonly value="Ключ">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" style="margin: 10px 0 20px 0;">
                            <div class="panel-group" id="accordion">
                                <div class="panel panel-default">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                        <div class="panel-heading" style="background: #f5f5f5;">
                                            <h4 class="panel-title" style="color: #333;">
                                                Изменить адрес объекта на карте
                                            </h4>
                                        </div>
                                    </a>
                                    <div id="collapseOne" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="col-md-12">
                                                <div id="map" style="width: 100%; height: 400px; margin-bottom: 15px"></div>
                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <?= $form->field($settingForm, 'latitude')->textInput(['id' => 'latitude', 'readonly'=> true]) ?> <br/>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <?= $form->field($settingForm, 'longitude')->textInput(['id' => 'longitude', 'readonly'=> true]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($settingForm, 'whatsapp')->widget(MaskedInput::className(), [
                                'mask' => 'https://w\a.me/7(999)9999999',
                                'clientOptions' => [
                                    'removeMaskOnSubmit' => true
                                ]
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($settingForm, 'instagram')->textInput(['placeholder' => 'Не указан']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($settingForm, 'youtube')->textInput(['placeholder' => 'Не указан']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($settingForm, 'facebook')->textInput(['placeholder' => 'Не указан']) ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app','Сохранить'), ['class' => 'login-button', 'name' => 'profile-button']) ?>
                </div>
            </form>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>