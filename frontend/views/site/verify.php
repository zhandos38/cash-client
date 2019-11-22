<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Активация аккаунта');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="login-box">
        <img src="/img/ims.png" alt="Logo">
        <div class="verify-msg">
            <p class="login-box-msg" style="font-size: 16px; color: #333; line-height: 40px; font-weight: 600;">Активация успешно выполнена!
                <br>
                Сейчас вас переадресует на страницу авторизации</p>
        </div>
    </div>
<?php
$js = <<<JS
    setTimeout(function() {
        var url = "/site/login";
        window.location.replace(url);
    }, 5000);
JS;
$this->registerJs($js);
?>