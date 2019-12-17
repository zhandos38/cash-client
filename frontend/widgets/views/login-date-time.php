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