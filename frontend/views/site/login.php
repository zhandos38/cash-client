<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use frontend\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
<img src="/img/ims.png" style="height: 110px; margin-bottom: 30px">

<p class="login-text">Платформа IMS приветствует Вас!</p>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

    <?= $form->field($model, 'password')->label(false)->passwordInput(['placeholder' => 'Введите пароль']) ?>

    <div class="form-group">
        <?= Html::submitButton('Войти', ['class' => 'login-button', 'name' => 'login-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<div class="info__wrapper" id="login-time">
    <div class="info__date"></div>
    <div class="info__time"></div>
</div>

<?php
$js =<<<JS
let gsDayNames = [
  'Воскресенье',
  'Понедельник',
  'Вторник',
  'Среда',
  'Четверг',
  'Пятница',
  'Суббота'
];
let gsMonthNames = [
  'Января',
  'Февраля',
  'Марта',
  'Апреля',
  'Мая',
  'Июня',
  'Июля',
  'Августа',
  'Сентября',
  'Октября',
  'Ноября',
  'Декабря',
];
function display_time() {
    let refresh = 1000; // Refresh rate in milli seconds
    setTimeout(() => {
        let currentDate = new Date();
        $('.info__time').html(currentDate.getHours() + ':' + currentDate.getMinutes() + ':' + currentDate.getUTCSeconds());
        let day = new Date().getDate();
        let dayOfWeek = new Date().getDay();
        let month = new Date().getMonth();    
        let year = new Date().getFullYear();    
        let dayName = gsDayNames[dayOfWeek];
        let monthName = gsMonthNames[month];
        $('.info__date').html(dayName + ', ' + day + ' ' + monthName + ', ' + year);
         display_time();
    }, refresh);
}
display_time();
JS;

$this->registerJs($js);
?>
