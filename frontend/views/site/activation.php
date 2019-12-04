<?php
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $objects [] */

$this->title = 'Инициализация';
?>

<a href="<?= Url::to('/site/logout') ?>" data-method="post" class="back-button"><i class="fa fa-reply-all" aria-hidden="true"></i> Выйти (<?php  if (!Yii::$app->user->isGuest) echo Yii::$app->user->identity->username;?>)</a>
<div class="activation">
    <div class="row">
        <?php foreach ($objects as $object): ?>
            <div class="col-xs-12 col-sm-4 col-md-4">
                <a class="admin-block" href="<?= Url::to(['site/activate', 'id' => $object['id']]) ?>" onclick="return confirm('Are you sure?')">
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
