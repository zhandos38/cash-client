<?php

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
use yii\helpers\Url; ?>

<a href="<?= Url::to(['site/index']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <a class="admin-block" href="<?= Url::to('/order/index') ?>">
            <div class="admin-block__item">
                <div class="admin-block__icon">
                    <i class="fa fa-clipboard" aria-hidden="true"></i>
                </div>
                <div class="admin-block__title">
                    Заказы
                </div>
            </div>
        </a>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <a class="admin-block" href="<?= Url::to('/order/create') ?>">
            <div class="admin-block__item">
                <div class="admin-block__icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div class="admin-block__title">
                    Создать заказ
                </div>
            </div>
        </a>
    </div>
</div>
