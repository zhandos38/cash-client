<?php
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $objects [] */

$this->title = 'Активация сервера';
?>

<div class="activation">
    <div class="row">
        <p class="activation-text">Выберите объект который хотите активировать</p>
        <?php foreach ($objects as $object): ?>
            <div class="col-xs-12 col-sm-4 col-md-4">
                <a class="admin-block" href="<?= Url::to(['site/activate', 'id' => $object['id']]) ?>" onclick="return confirm('Вы дейвствительно хотите активировать данный объект?')">
                    <div class="admin-block__item">
                        <div class="admin-block__icon">
                            <i class="<?= Yii::$app->object->getTypeIconLabelById($object['type_id']) ?>" aria-hidden="true"></i>
                        </div>
                        <div class="admin-block__title">
                            <?= $object['name'] ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
