<?php

$this->title = 'Накладные';
$this->params['breadcrumbs'][] = $this->title;
use yii\helpers\Url; ?>

<a href="<?= Url::to(['company-objects/report']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <a class="admin-block" href="<?= Url::to('/invoice/index') ?>">
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
    <div class="col-xs-12 col-sm-6 col-md-4">
        <a class="admin-block" href="<?= Url::to('/invoice/create') ?>">
            <div class="admin-block__item">
                <div class="admin-block__icon">
                    <i class="fas fa-dolly-flatbed" aria-hidden="true"></i>
                </div>
                <div class="admin-block__title">
                    Добавить накладную
                </div>
            </div>
        </a>
    </div>
</div>
