<?php


namespace frontend\models;


use common\models\User;
use Yii;
use yii\base\Model;

class EditProfile extends Model
{
    public $username;
    public $email;
    public $full_name;
    public $phone;

    public function rules()
    {
        return [
            [['username', 'email', 'full_name', 'phone'], 'string'],
        ];
    }

    public function save()
    {
        $user = User::findOne(Yii::$app->user->identity->id);
        return $user->save() ? true : false;
    }
}