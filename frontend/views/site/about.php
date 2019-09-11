<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\web\ForbiddenHttpException;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;

if (!\Yii::$app->user->can('view')) {
    throw new ForbiddenHttpException('Access denied!');
}
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>This is the About page. You may modify the following file to customize its content:</p>

    <code><?= __FILE__ ?></code>
</div>