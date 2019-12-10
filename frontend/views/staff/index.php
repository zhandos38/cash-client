<?php

use common\models\User;
use marekpetras\yii2ajaxboxwidget\Box;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\StaffSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Список сотрудников';
$this->params['breadcrumbs'][] = $this->title;
?>
<a href="<?= Url::to('/staff/main') ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div class="user-index">
    <h1>Список сотрудников</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'full_name',
            'phone',
            //'code_number',
            [
                'attribute' => 'role',
                'value' => function(User $model) {
                    return $model->getRoleLabel();
                },
                'filter' => User::getRoles()
            ],
            [
                'attribute' => 'status',
                'value' => function(User $model) {
                    return $model->getStatusLabel();
                },
                'filter' => User::getStatuses()
            ],
            [
                'attribute' => 'created_at',
                'value' => function(User $model) {
                    return date('m.d.Y H:i', $model->created_at);
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}'
            ]
        ],
    ]); ?>

</div>

<?php
Modal::begin([
    'header' => '<h4>Разрешения</h4>',
    'id' => 'staff-permissions-modal',
    'size' => 'modal-lg'
]);

echo '<div id="staff-permissions-modal__content"></div>';

Modal::end();
?>
<?php
$js =<<<JS
$(document).on("click", '.staff__permissions-btn', function() {
    $('#staff-permissions-modal').modal('show')
    .find('#staff-permissions-modal__content')
    .load('/staff/permissions', {'id': $( this ).data('id')});
});
JS;

$this->registerJs($js);
?>
