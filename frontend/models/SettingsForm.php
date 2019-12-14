<?php


namespace frontend\models;


use pheme\settings\models\Setting;
use yii\base\Model;
use yii\helpers\VarDumper;

class SettingsForm extends Model
{
    public $name;
    public $address;
    public $phone;
    public $latitude;
    public $longitude;
    public $whatsapp;
    public $facebook;
    public $instagram;
    public $youtube;

    private $_oldAttributes;

    public function rules()
    {
        return [
            [['name', 'address', 'phone','facebook', 'instagram', 'youtube', 'latitude', 'longitude', 'whatsapp'], 'string'],
            [['type_id', 'created_at'], 'integer'],
            [['name', 'address', 'phone'], 'required'],
            [['name', 'address', 'phone','facebook', 'instagram', 'youtube', 'latitude', 'longitude'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Наименование объекта',
            'phone' => 'Контактный телефон объекта',
            'address' => 'Адрес объекта',
            'latitude' => 'Широта',
            'longitude' => 'Долгота',
            'balance' => 'Баланс',
            'whatsapp' => 'WhatsApp контакт объекта',
            'instagram' => 'Instagram аккаунт',
            'facebook' => 'Facebook аккаунт',
            'youtube' => 'Youtube канал',
            'created_at' => 'Дата создания'
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
        $this->instagram = $settings->getInstagram();
        $this->whatsapp = $settings->getWhatsapp();
        $this->youtube = $settings->getYoutube();

        $this->_oldAttributes = $this->attributes;
    }

    public function save()
    {
        $settings = \Yii::$app->settings;
        if ($this->_oldAttributes['name'] != $this->name) {
            $settings->setName($this->name);
            $this->setIsUpdate('name');
        }
        if ($this->_oldAttributes['phone'] != $this->phone) {
            $settings->setPhone($this->phone);
            $this->setIsUpdate('phone');
        }
        if ($this->_oldAttributes['address'] != $this->address) {
            $settings->setAddress($this->address);
            $this->setIsUpdate('address');
        }
        if ($this->_oldAttributes['latitude'] != $this->latitude) {
            $settings->setLatitude($this->latitude);
            $this->setIsUpdate('latitude');
        }
        if ($this->_oldAttributes['longitude'] != $this->longitude) {
            $settings->setLongitude($this->longitude);
            $this->setIsUpdate('longitude');
        }
        if ($this->_oldAttributes['facebook'] != $this->facebook) {
            $settings->setFacebook($this->facebook);
            $this->setIsUpdate('facebook');
        }
        if ($this->_oldAttributes['instagram'] != $this->instagram) {
            $settings->setInstagram($this->instagram);
            $this->setIsUpdate('instagram');
        }
        if ($this->_oldAttributes['whatsapp'] != $this->whatsapp) {
            $settings->setWhatsapp($this->whatsapp);
            $this->setIsUpdate('whatsapp');
        }
         if ($this->_oldAttributes['youtube'] != $this->youtube) {
            $settings->setYoutube($this->youtube);
             $this->setIsUpdate('youtube');
        }
    }

    private function setIsUpdate($key)
    {
        $setting = Setting::findOne(['key' => $key]);
        $setting->is_updated = false;
        return $setting->save();
    }
}