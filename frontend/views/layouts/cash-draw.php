<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\widgets\NavWidget;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<header>
    <div class="container">
        <div class="user-info">
            <?php if (Yii::$app->user->isGuest):?>
                <a class="ims-title" href="<?= Url::to('/site/login') ?>"></a>
            <?php endif;?>
        </div>
    </div>
</header>

<div class="wrap">
    <div class="container-fluid">
        <?= Alert::widget() ?>
        <?= NavWidget::widget()?>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
