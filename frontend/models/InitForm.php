<?php
namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\httpclient\Client;

/**
 * Login form
 */
class InitForm extends Model
{
    public $username;
    public $password;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'string'],
            [['username', 'password'], 'required', 'message' => 'Введите "{attribute}"']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('user', 'Введите ИИН/БИН'),
            'password' => Yii::t('user', 'Пароль')
        ];
    }

    public function initialization()
    {
        $authManager = Yii::$app->authManager;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl(\Yii::$app->params['apiUrl'] . 'v1/init')
                ->setData(['username' => $this->username, 'password' => $this->password])
                ->send();

            if (!$response->isOk) {
                Yii::$app->session->setFlash('error', 'Неверный ИИН/БИН или пароль!');
                return false;
            }

            $responseData = Json::decode($response->content);
            $responseUser = $responseData['user'];
            $responseObjects = $responseData['objects'];

            if (!$responseObjects) {
                Yii::$app->session->setFlash('error', 'У Вас отсутствуют свободные объекты, привязка объекта не возможна! Вам необходимо создать объект в личном кабинете, затем осуществить привязку объекта.');
                return false;
            }

            $user = new User();
            $user->username = $responseUser['username'];
            $user->full_name = $responseUser['full_name'];
            $user->phone = $responseUser['phone'];
            $user->email = $responseUser['email'];
            $user->password_hash = $responseUser['password_hash'];
            $user->status = $responseUser['status'];
            $user->generateAuthKey();

            if (!$user->validate() || !$user->save())
                throw new Exception('User is not saved!');

            $authManager->assign($authManager->getRole(User::ROLE_DIRECTOR), $user->id);

            $this->login();
            $transaction->commit();

            return $responseObjects;

        } catch (Exception $exception) {
            $transaction->rollBack();

            throw new Exception($exception->getMessage());
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
            Yii::$app->user->login($this->getUser(), 3600 * 24);
            return true;
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
}
