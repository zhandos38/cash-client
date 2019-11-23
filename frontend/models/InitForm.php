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
    public $token;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token'], 'required', 'message' => 'Введите "{attribute}"'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'token' => Yii::t('user', 'Token')
        ];
    }

    public function activate()
    {
        $authManager = Yii::$app->authManager;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl(\Yii::$app->params['apiUrl'] . 'v1/activate')
                ->addHeaders(['Authorization' => 'Bearer ' . $this->token])
                ->addHeaders(['content-type' => 'application/json'])
                ->setData(['token' => $this->token])
                ->send();

            $responseData = Json::decode($response->content);
            $responseUser = $responseData['user'];
            $responseSettings = $responseData['settings'];

            $user = new User();
            $user->username = $responseUser['username'];
            $user->full_name = $responseUser['full_name'];
            $user->phone = $responseUser['phone'];
            $user->email = $responseUser['email'];
            $user->password_hash = $responseUser['password_hash'];
            $user->generateAuthKey();

            if (!$user->validate() || !$user->save())
                throw new Exception('User is not saved!');

            $authManager->assign($authManager->getRole(User::ROLE_DIRECTOR), $user->id);

            Yii::$app->settings->setName($responseSettings['name']);
            Yii::$app->settings->setBalance($responseSettings['balance']);
            Yii::$app->settings->setAddress($responseSettings['address']);
            Yii::$app->settings->setPhone($responseSettings['phone']);
            Yii::$app->settings->setToken($this->token);

            $transaction->commit();

        } catch (Exception $exception) {
            $transaction->rollBack();

            throw new Exception($exception->getMessage());
        }

        return true;
    }
}
