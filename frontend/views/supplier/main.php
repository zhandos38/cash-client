<?php

$this->title = 'Поставщики';
$this->params['breadcrumbs'][] = $this->title;
use yii\helpers\Url; ?>

<a href="<?= Url::to(['company-objects/report']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <a class="admin-block" href="<?= Url::to('/supplier/index') ?>">
            <div class="admin-block__item">
                <div class="admin-block__icon">
                    <i class="fa fa-users" aria-hidden="true"></i>
                </div>
                <div class="admin-block__title">
                    Список поставщиков
                </div>
            </div>
        </a>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <a class="admin-block" href="<?= Url::to('/supplier/create') ?>">
            <div class="admin-block__item">
                <div class="admin-block__icon">
                    <i class="fa fa-ambulance" aria-hidden="true"></i>
                </div>
                <div class="admin-block__title">
                    Добавить поставщика
                </div>
            </div>
        </a>
    </div>
</div>
