<?php

namespace common\models;

use Yii;
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
            ['is_sent', 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'started_at' => 'Started At',
            'closed_at' => 'Closed At',
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
