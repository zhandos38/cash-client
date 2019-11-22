<?php


namespace frontend\models;


use common\models\ShiftHistory;
use common\models\User;

class Staff extends User
{
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
}