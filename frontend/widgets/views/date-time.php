<?php
?>

<div class="info__wrapper">
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
  'Январь',
  'Февраль',
  'Март',
  'Апрель',
  'Май',
  'Июнь',
  'Июль',
  'Август',
  'Сентябрь',
  'Октябрь',
  'Ноябрь',
  'Декабрь',
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
        $('.info__date').html(dayName + ', ' + day + ' ' + monthName + ', ' + year + ' | ');
         display_time();
    }, refresh);
}
display_time();
JS;

$this->registerJs($js);
?>
