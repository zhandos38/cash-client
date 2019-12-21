<?php

use common\models\Category;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Категорий';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить категорию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'attribute' => 'parent_id',
                'value' => function(Category $model) {
                    return $model->parent->name;
                },
                'filter' => ArrayHelper::map(Category::find()->all(), 'id', 'name')
            ],
            'color_id',
            [
                'attribute' => 'is_active',
                'value' => function(Category $model) {
                    return $model->getCategoryLabel();
                },
                'filter' => Category::getCategoryLabels()
            ],
            [
                'attribute' => 'created_at',
                'value' => function(Category $model) {
                    return date('d/m/Y H:i', $model->created_at);
                }
            ],
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
