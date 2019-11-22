<?php

$this->title = 'Склад';
$this->params['breadcrumbs'][] = $this->title;
use yii\helpers\Url; ?>

<a href="<?= Url::to(['company-objects/report']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <a class="admin-block" href="<?= Url::to('/product/index') ?>">
            <div class="admin-block__item">
                <div class="admin-block__icon">
                    <i class="fa fa-cubes" aria-hidden="true"></i>
                </div>
                <div class="admin-block__title">
                    Склад
                </div>
            </div>
        </a>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <a class="admin-block" href="<?= Url::to('/product/create') ?>">
            <div class="admin-block__item">
                <div class="admin-block__icon">
                    <i class="fa fa-cube" aria-hidden="true"></i>
                </div>
                <div class="admin-block__title">
                    Добавить товар
                </div>
            </div>
        </a>
    </div>
</div>
