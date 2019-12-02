<?php

$this->title = 'Клиненты';
$this->params['breadcrumbs'][] = $this->title;
use yii\helpers\Url; ?>

<a href="<?= Url::to(['site/index']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <a class="admin-block" href="<?= Url::to('/customer/index') ?>">
            <div class="admin-block__item">
                <div class="admin-block__icon">
                    <i class="fa fa-users" aria-hidden="true"></i>
                </div>
                <div class="admin-block__title">
                    Список клиентов
                </div>
            </div>
        </a>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <a class="admin-block" href="<?= Url::to('/customer/create') ?>">
            <div class="admin-block__item">
                <div class="admin-block__icon">
                    <i class="fa fa-user" aria-hidden="true"></i>
                </div>
                <div class="admin-block__title">
                    Добавить клиента
                </div>
            </div>
        </a>
    </div>
</div>
