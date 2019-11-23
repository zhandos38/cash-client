<?php
namespace frontend\components;

use common\models\CompanyObjects;
use common\models\ShiftHistory;
use Exception;
use yii\base\Component;
use yii\base\ErrorException;
use yii\helpers\VarDumper;

class ObjectComponent extends Component
{
    public function setShift()
    {
        $lastShift = ShiftHistory::find()
            ->orderBy(['id' => SORT_DESC])
            ->where(['user_id' => \Yii::$app->user->identity->getId()])
            ->one();

        if (empty($lastShift)) {
            return $this->createShift();
        }

        if ($lastShift->status == ShiftHistory::STATUS_CLOSED) {
            \Yii::$app->session->set('shift_id', null);
            return false;
        } else {
            \Yii::$app->session->set('shift_id', $lastShift->id);
            return true;
        }
    }

    public function createShift()
    {
        $lastShift = $this->setShift();
        $shift = new ShiftHistory();
        if ($shift->save()) {
            \Yii::$app->session->set('shift_id', $shift->id);
            return true;
        } else {
            throw new ErrorException('Ошибка создания смены!');
        }
    }

    public function closeShift()
    {
        $id = $this->getShiftId();
        $shift = ShiftHistory::findOne(['id' => $id]);
        $shift->status = ShiftHistory::STATUS_CLOSED;
        $shift->closed_at = time();
        \Yii::$app->session->set('shift_id', null);
        if (!$shift->save())
            throw new ErrorException('Ошибка смена не закрыта!');
    }

    public function getShiftId()
    {
        return \Yii::$app->session->get('shift_id');
    }
}