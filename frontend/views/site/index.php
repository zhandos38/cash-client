<?php

/* @var $this yii\web\View */

$this->title = 'Главная';

use yii\helpers\Url; ?>

<a href="<?= Url::to('/site/logout') ?>" data-method="post" class="back-button"><i class="fa fa-reply-all" aria-hidden="true"></i> Выйти (<?php  if (!Yii::$app->user->isGuest) echo Yii::$app->user->identity->username;?>)</a>

<div class="site-index">
    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4">
            <a class="admin-block" href="<?= Url::to('/site/edit-profile') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fa fa-address-card" aria-hidden="true"></i>
                    </div>
                    <div class="admin-block__title">
                        Профиль
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-4" style="<?= Yii::$app->user->can('manageStaff') ? '' : 'display: none' ?>">
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
        <div class="col-xs-6 col-sm-6 col-md-4" style="<?= Yii::$app->user->can('manageCustomer') ? '' : 'display: none' ?>">
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
        <div class="col-xs-6 col-sm-6 col-md-4" style="<?= Yii::$app->user->can('manageWarehouse') ? '' : 'display: none' ?>">
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
        <div class="col-xs-6 col-sm-6 col-md-4" style="<?= Yii::$app->user->can('manageOrder') ? '' : 'display: none' ?>">
            <a class="admin-block" href="<?= Url::to('/order/index') ?>">
                <div class="admin-block__item">
                    <div class="admin-block__icon">
                        <i class="fas fa-clipboard" aria-hidden="true"></i>
                    </div>
                    <div class="admin-block__title">
                        Заказы
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-4" style="<?= Yii::$app->user->can('manageInvoice') ? '' : 'display: none' ?>">
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
        <div class="col-xs-6 col-sm-6 col-md-4" style="<?= Yii::$app->user->can('manageSupplier') ? '' : 'display: none' ?>">
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
        <div class="col-xs-6 col-sm-6 col-md-4" style="<?= Yii::$app->user->can('createOrder') ? '' : 'display: none' ?>">
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
        <?php if (!Yii::$app->object->getShiftId()): ?>
            <div class="col-xs-6 col-sm-6 col-md-4" style="<?= Yii::$app->user->can('createOrder') ? '' : 'display: none' ?>">
                <a class="admin-block" href="<?= Url::to(['/staff/open-shift']) ?>">
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
        <?php else: ?>
            <div class="col-xs-6 col-sm-6 col-md-4" style="<?= Yii::$app->user->can('createOrder') ? '' : 'display: none' ?>">
                <a class="admin-block" href="<?= Url::to('/staff/close-shift') ?>">
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
        <div class="col-xs-6 col-sm-6 col-md-4">
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
</div>


