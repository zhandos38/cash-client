<?php
namespace backend\forms;

use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use common\models\User;
use yii\db\Exception;
use yii\helpers\VarDumper;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $id;
    public $username;
    public $full_name;
    public $address;
    public $email;
    public $password;
    public $role;
    public $status;
    public $phone;
    public $company_id;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            [['full_name', 'address', 'role', 'phone'], 'string'],
            [['status', 'company_id'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'full_name' => 'Ф.И.О',
            'password' => 'Пароль',
            'phone' => 'Телефон',
            'address' => 'Адрес',
            'company_id' => 'Компания',
            'role' => 'Роль',
            'status' => 'Статус'
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     * @throws ErrorException
     * @throws \yii\base\Exception
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->password = $this->password;
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->full_name = $this->full_name;
        $user->address = $this->address;
        $user->phone = $this->phone;
        $user->company_id = $this->company_id;
        $user->status = $this->status;
        $user->role = $this->role;
        try {
            if (!$user->save()) {
                throw new ErrorException('Error: User не создан!');
            }
        } catch (\ErrorException $exception) {
            throw new ErrorException($exception->getMessage());
        }
        $this->id = $user->id;
        return true;
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
