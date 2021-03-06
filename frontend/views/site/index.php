<?php

/* @var $this yii\web\View */

$this->title = 'Главная';

use frontend\assets\MenuAsset;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\Pjax;

MenuAsset::register($this);
?>
<div id="menu-app" class="site-index">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-3" style="<?= Yii::$app->user->can('manageStaff') ? '' : 'display: none' ?>">
            <a class="admin-block" href="<?= Url::to('#') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="admin-block__title">
                        Отчеты
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3" style="<?= Yii::$app->user->can('manageOrder') ? '' : 'display: none' ?>">
            <a class="admin-block" href="<?= Url::to('/order/index') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-clipboard" aria-hidden="true"></i>
                    </div>
                    <div class="admin-block__title">
                        Заказы / Чеки
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3" style="<?= Yii::$app->user->can('manageWarehouse') ? '' : 'display: none' ?>">
            <a class="admin-block" href="<?= Url::to('/product/main') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-inventory" aria-hidden="true"></i>
                    </div>
                    <div class="admin-block__title">
                        Склад
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3" style="<?= Yii::$app->user->can('manageInvoice') ? '' : 'display: none' ?>">
            <a class="admin-block" href="<?= Url::to('/invoice/main') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-file-invoice" aria-hidden="true"></i>
                    </div>
                    <div class="admin-block__title">
                        Накладные
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3" style="<?= Yii::$app->user->can('manageStaff') ? '' : 'display: none' ?>">
            <a class="admin-block" href="<?= Url::to('/staff/main') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-address-card" aria-hidden="true"></i>
                    </div>
                    <div class="admin-block__title">
                        Сотрудники
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-3" style="<?= Yii::$app->user->can('manageCustomer') ? '' : 'display: none' ?>">
            <a class="admin-block" href="<?= Url::to('/customer/main') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-users" aria-hidden="true"></i>
                    </div>
                    <div class="admin-block__title">
                        Клиенты
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3" style="<?= Yii::$app->user->can('manageSupplier') ? '' : 'display: none' ?>">
            <a class="admin-block" href="<?= Url::to('/supplier/main') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-industry-alt" aria-hidden="true"></i>
                    </div>
                    <div class="admin-block__title">
                        Поставщики
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3" style="<?= Yii::$app->user->can('manageSupplier') ? '' : 'display: none' ?>">
            <a class="admin-block" href="<?= Url::to('/site/object-settings') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="admin-block__title">
                        Настройки объекта
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3" style="<?= Yii::$app->user->can('manageShift') ? '' : 'display: none' ?>">
            <a class="admin-block" href="<?= Url::to(['shift/index']) ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="admin-block__title">
                        Смены
                    </div>
                </div>
            </a>
        </div>
        <?php if (Yii::$app->user->can('createOrder')): ?>
            <div class="col-xs-6 col-sm-6 col-md-3" @click="openShiftOpenModal" v-show="openShiftActive">
                <a class="admin-block" href="javascript:void(0)">
                    <div class="admin-block__item">
                        <div class="admin-block__icon">
                            <i class="fas fa-hourglass-start" aria-hidden="true"></i>
                        </div>
                        <div class="admin-block__title">
                            Открыть смену
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-3" @click="openShiftCloseModal" v-show="!openShiftActive">
                <a class="admin-block" href="javascript:void(0)">
                    <div class="admin-block__item">
                        <div class="admin-block__icon">
                            <i class="fas fa-flag-checkered" aria-hidden="true"></i>
                        </div>
                        <div class="admin-block__title">
                            Закрыть смену
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>
        <div class="col-xs-6 col-sm-6 col-md-3" style="<?= Yii::$app->user->can('createOrder') ? '' : 'display: none' ?>">
            <a class="admin-block" href="<?= Url::to('/order/test-create') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-store" aria-hidden="true"></i>
                    </div>
                    <div class="admin-block__title">
                        Режим магазина
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3">
            <a class="admin-block" href="<?= Url::to('/cash-draw/index') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-cash-register" aria-hidden="true"></i>
                    </div>
                    <div class="admin-block__title">
                        Касса
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="shift-modal__wrapper" v-show="shiftCloseModalActive">
        <div class="shift-close-modal">
            <div class="comment-modal__close pull-right" @click="closeShiftCloseModal">
                <i class="fas fa-times"></i>
            </div>
            <div class="shift-modal__container">
                <h2>Закрыть смену</h2>
                <div class="shift-close__modal-content">
                    <div class="shift-modal__info">
                        <div class="shift-modal__balance">{{ cashBoxBalance }}</div>
                        <div class="shift-modal__cashier">
                            <p><span>Кассир:</span> <?= Yii::$app->user->identity->full_name ?></p>
                        </div>
                        <div class="shift-modal__date">
                            <p><span>Дата/Время:</span> <label id="current-date">{{ currentTime }}</label></p>
                        </div>
                    </div>
                    <div class="numpad-wrapper">
                        <div class="numpad">
                            <button type="button" class="numpad__button" @click="setBalance('1')">1</button>
                            <button type="button" class="numpad__button" @click="setBalance('2')">2</button>
                            <button type="button" class="numpad__button" @click="setBalance('3')">3</button>
                            <button type="button" class="numpad__button" @click="setBalance('4')">4</button>
                            <button type="button" class="numpad__button" @click="setBalance('5')">5</button>
                            <button type="button" class="numpad__button" @click="setBalance('6')">6</button>
                            <button type="button" class="numpad__button" @click="setBalance('7')">7</button>
                            <button type="button" class="numpad__button" @click="setBalance('8')">8</button>
                            <button type="button" class="numpad__button" @click="setBalance('9')">9</button>
                            <button type="button" class="numpad__button" @click="setBalance('.')">,</button>
                            <button type="button" class="numpad__button" @click="setBalance('0')">0</button>
                            <button type="button" class="numpad__button" @click="cleanBalance">C</button>
                        </div>
                        <div class="numpad-extension">
                            <button type="button" class="numpad-extension__button" @click="setBalance(0.5)">+0.5</button>
                            <button type="button" class="numpad-extension__button" @click="setBalance(1)">+1</button>
                            <button type="button" class="numpad-extension__button" @click="setBalance(2)">+2</button>
                            <button type="button" class="numpad-extension__button" @click="setBalance(5)">+5</button>
                            <button type="button" class="numpad-extension__button" @click="setBalance(10)">+10</button>
                            <button type="button" class="numpad-extension__button" @click="setBalance(20)">+20</button>
                            <button type="button" class="numpad-extension__button" @click="setBalance(50)">+50</button>
                            <button type="button" class="numpad-extension__button" @click="setBalance(100)">+100</button>
                        </div>
                    </div>
                </div>
                <div class="shift-modal-close__btn" @click="closeShift">
                    Закрыть смену
                </div>
            </div>
        </div>
    </div>
    <div class="shift-modal__wrapper" v-show="shiftOpenModalActive">
        <div class="shift-modal">
            <div class="shift-modal__close pull-right" @click="closeShiftOpenModal">
                <i class="fas fa-times"></i>
            </div>
            <div class="shift-modal__container">
                <h2>Открыть смену</h2>
                <div class="shift-modal__cashier">
                    <p><span>Кассир:</span> <?= Yii::$app->user->identity->full_name ?></p>
                </div>
                <div class="shift-modal__date">
                    <p><span>Дата/Время:</span> <label id="current-date">{{ currentTime }}</label></p>
                </div>
                <div class="shift-modal__warning">
                    <p>Пожалуйста, сверьте время</p>
                </div>
                <div class="shift-modal__btn" @click="setShift">
                    <a href="">Открыть смену</a>
                </div>
            </div>
        </div>
    </div>
</div>

