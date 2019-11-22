<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "shift_transactions".
 *
 * @property int $id
 * @property int $sum
 * @property int $type_id
 * @property int $user_id
 * @property int $comment
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 * @property int $shift_id [int(11)]
 */
class ShiftTransactions extends \yii\db\ActiveRecord
{
    const TYPE_INSERT = 0;
    const TYPE_TAKEN = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shift_transactions';
    }

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
            [['user_id', 'created_at', 'updated_at', 'shift_id'], 'integer'],
            [['sum', 'type_id'], 'number'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['comment', 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sum' => 'Sum',
            'type_id' => 'Type ID',
            'user_id' => 'User ID',
            'shift_id' => 'Shift ID',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
