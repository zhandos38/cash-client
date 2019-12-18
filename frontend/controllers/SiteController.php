<?php
namespace frontend\controllers;

use frontend\models\InitForm;
use common\models\User;
use frontend\models\AddStaffForm;
use frontend\models\ChangePasswordForm;
use frontend\models\EditProfile;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\SettingsForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidParamException;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\ContactForm;
use yii\web\ForbiddenHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['login', 'request-password-reset', 'reset-password', 'verify', 'error', 'init'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $this->layout = '@app/views/layouts/login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (!Yii::$app->settings->getToken()) {
            return $this->redirect(['site/init']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionInit()
    {
        $this->layout = '@app/views/layouts/login';

        $model = new InitForm();
        if ($model->load(Yii::$app->request->post())) {
            $objects = $model->initialization();
            Yii::$app->session->set('objects', $objects);
            return $this->redirect(['site/activate']);
        }

        return $this->render('init', [
            'model' => $model
        ]);
    }

    public function actionActivate($id = null)
    {
        $objects = Yii::$app->session->get('objects');

        if (!$objects)
            throw new ForbiddenHttpException();

        if ($id) {
            $authManager = Yii::$app->authManager;

            $transaction = Yii::$app->db->beginTransaction();
            try {
                /* Getting serial number */
                $serialNumber = shell_exec('wmic DISKDRIVE GET SerialNumber 2>&1');
                $serialNumber = md5($serialNumber);

                $client = new Client();
                $response = $client->createRequest()
                    ->setMethod('GET')
                    ->setUrl(\Yii::$app->params['apiUrl'] . 'v1/activate')
                    ->setData(['id' => $id, 'serialNumber' => $serialNumber])
                    ->send();

                if ($response->content == 'false') {
                    Yii::$app->session->setFlash('error', 'Данный токен не найден или уже активирован');
                    $transaction->rollBack();
                    return false;
                }

                $responseData = Json::decode($response->content);
                $responseSettings = $responseData['settings'];

                Yii::$app->settings->setName($responseSettings['name']);
                Yii::$app->settings->setBalance($responseSettings['balance']);
                Yii::$app->settings->setAddress($responseSettings['address']);
                Yii::$app->settings->setPhone($responseSettings['phone']);
                Yii::$app->settings->setToken($responseSettings['token']);
                Yii::$app->settings->setExpiredAt($responseSettings['expired_at']);
                Yii::$app->settings->setIsActivated($responseSettings['is_activated']);
                Yii::$app->settings->setLongitude($responseSettings['longitude']);
                Yii::$app->settings->setLatitude($responseSettings['latitude']);
                Yii::$app->settings->setTypeId($responseSettings['type_id']);
                Yii::$app->settings->setCreatedAt($responseSettings['created_at']);
                if (!empty($responseSettings['whatsapp']))
                    Yii::$app->settings->setWhatsapp($responseSettings['whatsapp']);
                if (!empty($responseSettings['facebook']))
                    Yii::$app->settings->setFacebook($responseSettings['facebook']);
                if (!empty($responseSettings['instagram']))
                    Yii::$app->settings->setInstagram($responseSettings['instagram']);
                if (!empty($responseSettings['youtube']))
                    Yii::$app->settings->setYoutube($responseSettings['youtube']);
                Yii::$app->settings->setSerialNumber($serialNumber);

                Yii::$app->session->setFlash('warning', 'У Вас установлен пароль по умолчанию, с целью безопасности измените пароль!');
                Yii::$app->session->remove('objects');
                $transaction->commit();

            } catch (Exception $exception) {
                $transaction->rollBack();

                throw new Exception($exception->getMessage());
            }

            return $this->redirect(['site/index']);
        }

        return $this->render('activation', [
            'objects' => $objects
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendRequest(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Проверьте свою электронную почту для получения дальнейших инструкций.'));

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Извините, мы не можем сбросить пароль для указанного ИИН/БИН.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            return $this->redirect('/site/login');
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Новый пароль сохранен.'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    public function actionVerify($token){
        try {
            $model = User::findOne(['password_reset_token'=>$token]);
            if($model){
                $model->status = User::STATUS_ACTIVE;
                $model->removePasswordResetToken();
                $model->save();
            }else{
                return $this->redirect('/site/login');
            }
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        return $this->render('verify', [
            'model' => $model,
        ]);
    }

    public function actionEditProfile()
    {
        $editForm = new EditProfile();
        $user = User::findOne(Yii::$app->user->identity->id);

        $model = new ChangePasswordForm();
        if (Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            if ($model->validate() && $model->save()){
                Yii::$app->session->setFlash('success','Пароль успешно изменен!');
                return $this->redirect('/site/index');
            }else{
                Yii::$app->session->setFlash('danger','Произошла ошибка при смене пароля');
            }
        }
        return $this->render('edit-profile', [
            'user' => $user,
            'editForm' => $editForm,
            'model' => $model,
        ]);
    }

    public function actionObjectSettings()
    {
        $settingForm = new SettingsForm();

        if ($settingForm->load(Yii::$app->request->post())) {
            $settingForm->save();
            Yii::$app->session->setFlash('success','Данные объекта успешно изменены!');
            return $this->redirect('/site/index');
        } else {
            $settingForm->init();
        }

        return $this->render('object-settings', [
            'settingForm' => $settingForm,
            ]);
    }
}
