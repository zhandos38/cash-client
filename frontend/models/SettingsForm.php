<?php


namespace frontend\models;


use yii\base\Model;

class SettingsForm extends Model
{
    public $name;
    public $address;
    public $phone;
    public $latitude;
    public $longitude;
    public $whatsapp;
    public $facebook;
    public $intagram;

    private $_oldAttributes;

    public function rules()
    {
        return [
            [['name', 'address', 'phone','facebook', 'intagram'], 'string'],
            [['latitude', 'longitude'], 'integer'],
        ];
    }

    public function init()
    {
        $settings = \Yii::$app->settings;
        $this->name = $settings->getName();
        $this->phone = $settings->getPhone();
        $this->address = $settings->getAddress();
        $this->latitude = $settings->getLatitude();
        $this->longitude = $settings->getLongitude();
        $this->facebook = $settings->getFacebook();
        $this->intagram = $settings->getInstagram();
        $this->whatsapp = $settings->getWhatsapp();
        $this->youtube = $settings->getYoutube();

        $this->_oldAttributes = $this->attributes;
    }

    public function save()
    {

    }
}