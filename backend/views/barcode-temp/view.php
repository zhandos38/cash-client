<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\BarcodeTemp */
?>
<div class="barcode-temp-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'number',
            'name',
            'img',
            'company_id',
        ],
    ]) ?>

</div>
