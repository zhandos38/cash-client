<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
if ($exception->getCode() == 1045) {
    $this->title = 'Ошибка 002';
}
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Обратитесь в техническую поддержку: +7(777)777-77-77
    </p>

</div>
