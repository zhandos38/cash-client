<?php
namespace backend\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['password', 'username'], 'required', 'message' => 'Введите "{attribute}"'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('user', 'Username'),
            'rememberMe' => Yii::t('user', 'Remember Me'),
            'password' => Yii::t('user', 'Password'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByPassword();
            if (!$user){
                $this->addError($attribute, 'Неверный ИИН/БИН или пароль!');
            }else
                if ($user && $user->status == User::STATUS_INACTIVE){
                    $this->addError($attribute, 'Ваш аккаунт не активирован, для активации аккаунта сбросьте пароль!');
                }else
            if ($user && !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный ИИН/БИН или пароль!');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $flag = Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
            return $flag;
        }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * Find user by [[password]]
     *
     * @return User|null
     */
    protected function getUserByPassword()
    {
        if ($this->_user === null) {
            $this->_user = User::findByPassword($this->password);
        }

        return $this->_user;
    }
}
