<?php
$actionName = Yii::$app->controller->action->id;
use yii\helpers\Url;
?>

<section class="cash-navigator">
    <ul>
        <li><a <?php if ($actionName == 'index'): ?> class="nav-active" <?php endif; ?> href="<?= Url::to('/cash-draw/index') ?>">Касса</a></li>
        <li><a <?php if ($actionName == 'orders'): ?> class="nav-active" <?php endif; ?> href="<?= Url::to('/cash-draw/orders') ?>">Чеки</a></li>
        <li><a href="#">Свободный возврат</a></li>
    </ul>
</section>
