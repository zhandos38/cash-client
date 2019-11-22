<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<style>
    .todo {
        text-decoration:none;display: inline-block;color: white;background: #f76f45;border: solid #f76f45;border-width: 10px 20px 8px;font-weight: bold;border-radius: 4px;
    }
</style>
<table style="width: 100% !important;height: 100%;background: #fff;-webkit-font-smoothing: antialiased;-webkit-text-size-adjust: none;color: #49555b;">
    <tr>
        <td style="display: block !important;clear: both !important;margin: 0 auto !important;max-width: 580px !important;">
            <table>
                <tr>
                    <td align="center">
                        <img src="http://www.ims-tmt.kz/img/ims.png" width="auto" height="200px" alt="IMS" style="margin-top: 30px">
                    </td>
                </tr>
                <tr>
                    <td style="padding: 30px 35px;border-spacing: 0; text-align: center">
                        <h2 style="font-size: 1.4em">Приветствуем Вас уважаемый <?= Html::encode($user->full_name) ?>,</h2>
                        <p>От Вас поступил запрос на сброс пароля по доступу в платформу "IMS".</p><br>
                        <p>Для сброса пароля нажмите на кнопку "Сбросить пароль".</p><br>
                        <table width="100%">
                            <tr>
                                <td align="center">
                                    <p>
                                        <a href="<?= Html::encode($resetLink) ?>" style=" text-decoration:none;display: inline-block;color: white;background: #00d5d4;border: solid #00d5d4;border-width: 10px 20px 8px;border-radius: 4px;">Сбросить пароль</a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <p><em>– С уважением к Вам и вашему бизнесу, Администрация платформы "IMS"</em></p>
                        <p style="font-size: 12px">Это письмо сгенерировано системой автоматический, если вы не регистрировались на нашем сайте, просьба проигнорируйте данное сообщение. </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table width="100%">
                <tr>
                    <td style="background: none;" class="content footer" align="center">
                        <p style="margin-bottom: 0;color: #888;text-align: center;font-size: 14px;">В случае возникновения вопросов Вы можете обратиться в нашу службу технической поддержки по телефону <br/><a  style="color: #888;text-decoration: none;font-weight: bold;" href="tel:+77777777777">+7 777 777 7777</a>.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
