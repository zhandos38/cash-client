<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Активация сервера';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <img src="/img/ims.png" style="height: 110px; margin-bottom: 30px">

    <p class="login-text">Вас приветствует платформа IMS!</p>
    <p class="init-text">Это первый запуск сервера IMS.</p>
    <p class="init-text">Вам необходимо осуществить активацию сервера. Для этого Вам необходимо авторизоваться, введя логин и пароль, затем выбрать объект, который Вы хотите активировать.</p><br/>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

    <?= $form->field($model, 'username')->textInput(['value' => $model->username, 'placeholder' => 'Введите ИИН/БИН'])->label(false) ?>

    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Введите пароль'])->label(false) ?>

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
        let day = new Date().getDay();    
        let month = new Date().getMonth();    
        let year = new Date().getFullYear();    
        let dayName = gsDayNames[day];
        let monthName = gsMonthNames[month];
        $('.info__date').html(dayName + ', ' + day + ' ' + monthName + ', ' + year);
         display_time();
    }, refresh);
}
display_time();
JS;

$this->registerJs($js);
?>