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

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['password'], 'required', 'message' => 'Введите "{attribute}"'],
            // password is validated by validatePassword()
            ['password', 'validatePinPassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
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
    public function validatePinPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByPassword();
            if (!$user) {
                $this->addError($attribute, 'Вы ввели неккоректный пароль!');
            } elseif ($user && ($user->status == User::STATUS_BLOCKED || $user->status == User::STATUS_FIRED)) {
                $this->addError($attribute, 'Ваша учетная запись заблокирована, обратитесь к руководству!');
            } elseif ($user && !$user->validatePinPassword($this->password)) {
                $this->addError($attribute, 'Неверный пароль!');
            } elseif (!Yii::$app->settings->checkFinalExpireDate()) {
                $this->addError($attribute,'Срок лицензии истек! Для корректной работы программы приобретите лицензию!');
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
        Yii::$app->settings->clearCache();
        if ($this->validate() && $this->checkSerialNumberLocal()) {
            Yii::$app->settings->checkFinalExpireDate();
            $flag = Yii::$app->user->login($this->getUserByPassword(), 0);
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
            ->setUrl(\Yii::$app->params['apiUrl'] . 'v1/validate')
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
            Yii::$app->session->setFlash('error', 'Ошибка 001');
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
