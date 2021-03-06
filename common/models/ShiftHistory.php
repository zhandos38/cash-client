<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "shift_history".
 *
 * @property int $id
 * @property int $user_id
 * @property int $status
 * @property int $started_at
 * @property int $closed_at
 *
 * @property User $user
 * @property bool $is_sent [tinyint(1)]
 * @property float $sum_at_close [float]
 */
class ShiftHistory extends \yii\db\ActiveRecord
{
    const STATUS_OPENED = 1;
    const STATUS_CLOSED = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shift_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'started_at', 'closed_at'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['is_sent', 'boolean'],
            ['sum_at_close', 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Работник',
            'status' => 'Статус',
            'started_at' => 'Время открытия',
            'closed_at' => 'Время закрытия',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getTransactions()
    {
        return $this->hasMany(ShiftTransactions::className(), ['shift_id' => 'id']);
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_CLOSED => 'Закрыта',
            self::STATUS_OPENED => 'Открыта',
        ];
    }

    public function getStatusLabel()
    {
        return ArrayHelper::getValue(self::getStatuses(), $this->status);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord)
        {
            $this->started_at = time();
            $this->user_id = Yii::$app->user->identity->getId();
        }

        return parent::beforeSave($insert);
    }
}
