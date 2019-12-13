<?php


namespace frontend\models;


use yii\base\Model;

class ObjectSettingsForm extends Model
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
}