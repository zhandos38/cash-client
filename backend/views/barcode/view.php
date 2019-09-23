<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Barcode */
?>
<div class="barcode-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'number',
            'name',
            'img',
        ],
    ]) ?>

</div>
