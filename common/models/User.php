<?php
namespace common\models;

use backend\modules\rbac\models\AuthAssignment;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\rbac\Assignment;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $auth_key
 * @property string $full_name
 * @property string $phone
 * @property integer $status
 * @property integer $role
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password
 * @property int $code_number [int(11)]
 * @property bool $is_sent [tinyint(1)]
 * @property int $exported_at [int(11)]
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_FIRED = 0;
    const STATUS_BLOCKED = 9;
    const STATUS_ACTIVE = 10;

    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_DIRECTOR = 'director';
    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_CASHIER = 'cashier';

    const DEFAULT_PASSWORD = '0000';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'role', 'code_number', 'phone', 'password'], 'string'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_FIRED, self::STATUS_BLOCKED]],

            ['is_sent', 'boolean'],
            ['exported_at', 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'full_name' => 'Ф.И.О',
            'phone' => 'Телефон',
            'code_number' => 'Номер карты',
            'role' => 'Роль',
            'status' => 'Статус',
            'password' => 'Пароль',
            'created_at' => 'Дата добавление',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->joinWith('tokens t')
            ->andWhere(['t.token' => $token])
            ->andWhere(['>', 't.expired_at', time()])
            ->one();
    }

    public static function findByPassword($password)
    {
        $foundUser = null;
        $users = static::find()->all();
        foreach ($users as $user) {
            if ($password == $user->password) {
                $foundUser = $user;
            }
        }
        return $foundUser;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePinPassword($password)
    {
        if ($password == $this->password)
            return true;
        else
            return false;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function getRolesForBackend()
    {
        return [
            self::ROLE_ADMIN => 'Админ',
            self::ROLE_DIRECTOR => 'Директор',
            self::ROLE_MANAGER => 'Менеджер',
            self::ROLE_ADMINISTRATOR => 'Администратор',
            self::ROLE_CASHIER => 'Кассир'
        ];
    }

    public static function getRoles()
    {
        return [
            self::ROLE_ADMINISTRATOR => 'Администратор',
            self::ROLE_CASHIER => 'Кассир'
        ];
    }

    /**
     * @return mixed
     */
    public function getRoleLabel()
    {
        return ArrayHelper::getValue(static::getRoles(), $this->status);
    }

    public function setDefaultPassword()
    {
        $this->password = self::DEFAULT_PASSWORD;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShifts()
    {
        return $this->hasMany(ShiftHistory::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastShift()
    {
        return $this->hasOne(ShiftHistory::class, ['user_id' => 'id'])
            ->onCondition(['status' => ShiftHistory::STATUS_OPENED]);
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_FIRED => 'Уволен',
            self::STATUS_BLOCKED => 'Заблокирован',
            self::STATUS_ACTIVE => 'Включен'
        ];
    }

    /**
     * @return mixed
     */
    public function getStatusLabel()
    {
        return ArrayHelper::getValue(static::getStatuses(), $this->status);
    }
}
