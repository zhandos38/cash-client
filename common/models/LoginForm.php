<?php
namespace common\models;

use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\httpclient\Client;

/**
 * Login form
 */
class LoginForm extends Model
{
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
            [['password'], 'required', 'message' => 'Введите "{attribute}"'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
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
        if ($this->validate() && $this->checkSerialNumberLocal()) {
            $flag = Yii::$app->user->login($this->getUserByPassword(), $this->rememberMe ? 3600 * 24 * 30 : 0);
            Yii::$app->object->setShift();
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

    public function checkSerialNumber()
    {
        $serialNumber = Yii::$app->settings->getSerialNumber();
        $token = \Yii::$app->settings->getToken();

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl(\Yii::$app->params['apiUrlDev'] . 'v1/validate')
            ->addHeaders(['Authorization' => 'Bearer ' . $token])
            ->addHeaders(['content-type' => 'application/json'])
            ->setData(['token' => $token, 'serialNumber' => $serialNumber])
            ->send();

        if ($response->statusCode == '403') {
            Yii::$app->session->setFlash('error', 'Authorization error!');
            return false;
        }

        return true;
    }

    public function checkSerialNumberLocal()
    {
        $serialNumber = shell_exec('wmic DISKDRIVE GET SerialNumber 2>&1');
        $serialNumber = md5($serialNumber);

        $localSerialNumber = Yii::$app->settings->getSerialNumber();

        if ($serialNumber == $localSerialNumber)
            return true;
        else {
            Yii::$app->session->setFlash('error', 'Authorization error!');
            return false;
        }
    }

    private function debug($var)
    {
        $fp = fopen("c:/test.txt", "w");
        fwrite($fp, VarDumper::dumpAsString($var, 10));
        fclose($fp);
    }
}
