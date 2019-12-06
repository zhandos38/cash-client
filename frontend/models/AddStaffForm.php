<?php
namespace frontend\models;

use Exception;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use common\models\User;
use yii\helpers\VarDumper;

/**
 * Signup form
 */
class AddStaffForm extends Model
{
    public $full_name;
    public $address;
    public $email;
    public $password;
    public $role;
    public $status;
    public $phone;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 4],
            ['password', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This password has already been taken.'],

            [['full_name', 'address', 'role', 'phone'], 'string'],
            [['status'], 'integer'],
            [['role', 'full_name', 'phone'], 'required'],
            [['role', 'full_name', ], 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'full_name' => 'Ф.И.О',
            'password' => 'Пароль',
            'phone' => 'Телефон',
            'address' => 'Адрес',
            'role' => 'Роль'
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     * @throws ErrorException
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function signup()
    {
        $authManager = Yii::$app->authManager;

        if (!$this->validate()) {
            return null;
        }

        Yii::$app->db->beginTransaction();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = new User();
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->generateEmailVerificationToken();
            $user->full_name = $this->full_name;
            $user->address = $this->address;
            $user->phone = $this->phone;
            $user->role = $this->role;
            $user->status = User::STATUS_ACTIVE;

            if ($user->save()) {
                $authManager->assign($authManager->getRole($user->getRoleLabel()), $user->id);
            } else {
                throw new Exception('Staff is not created!');
            }

            $transaction->commit();
        } catch (Exception $exception) {
            $transaction->rollBack();
            throw new Exception($exception->getMessage());
        }

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
