<?php


namespace frontend\models;


use common\models\User;
use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $password;

    public function rules()
    {
        return [
            [['password'], 'required', 'message' => 'Введите новый пароль.'],
            ['password', 'string','min' => 6],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => Yii::t('user','Password')
        ];
    }

    public function save()
    {
        $user = User::findOne(Yii::$app->user->identity->id);
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        return $user->save() ? true : false;
    }
}