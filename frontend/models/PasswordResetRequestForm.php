<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $username;
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required', 'message' => 'Введите "{attribute}"'],
            ['username', 'exist',
                'targetClass' => '\common\models\User',
                'message' => 'Данный ИИН/БИН не зарегистрирован в системе!'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('user', 'Username'),
        ];
    }

    public function sendRequest()
    {

        $email = User::findOne(['email' => $this->email]);

        if ($this->$email) {
            return $this->sendEmail();
        }
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */

    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne(['username' => $this->username]);

        if (!$user) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => 'Платформа IMS'])
            ->setTo($user->email)
            ->setSubject('Запрос на сброс пароля')
            ->send();
    }
}
