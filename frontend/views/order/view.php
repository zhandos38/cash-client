<?php

use common\models\Order;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Заказ #' . $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-view">

    <h1>Заказ #<?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Назад', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'created_by',
                'value' => function(Order $model) {
                    return $model->createdBy->full_name;
                }
            ],
            [
                'attribute' => 'customer_id',
                'value' => function(Order $model) {
                    return $model->customer->full_name;
                }
            ],
            [
                'attribute' => 'order_items',
                'value' => function(Order $model) {
                    $output = '';
                    foreach ($model->orderItems as $item) {
                        $output .= $item->name . ' - ' . $item->real_price . ' x ' . $item->quantity . '<br>';
                    }
                    return $output;
                },
                'format' => 'html',
                'label' => 'Товары'
            ],
            'cost',
            [
                'attribute' => 'status',
                'value' => function(Order $model) {
                    return $model->getStatusLabel();
                }
            ],
            [
                'attribute' => 'is_debt',
                'value' => function(Order $model) {
                    return $model->getIsDebtStatusLabel();
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function(Order $model) {
                    return date('d/m/Y H:i', $model->created_at);
                }
            ],
            [
                'attribute' => 'updated_at',
                'value' => function(Order $model) {
                    return date('d/m/Y H:i', $model->updated_at);
                }
            ],
        ],
    ]) ?>

</div>
