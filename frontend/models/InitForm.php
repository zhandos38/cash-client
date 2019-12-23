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
 * Init form
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
            $responseObjects = $responseData['objects'];
            if (!$responseObjects) {
                Yii::$app->session->setFlash('error', 'У Вас отсутствуют свободные объекты, активация объекта не возможна! Вам необходимо создать объект в личном кабинете, затем осуществить активацию объекта.');
                return false;
            }
            $transaction->commit();
            return $responseObjects;
        } catch (Exception $exception) {
            $transaction->rollBack();
            throw new Exception($exception->getMessage());
        }
    }
}