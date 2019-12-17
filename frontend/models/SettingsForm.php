<?php


namespace frontend\models;


use pheme\settings\models\Setting;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class SettingsForm extends Model
{
    const TYPE_SHOP = 0;
    const TYPE_RESTAURANT = 1;

    public $name;
    public $address;
    public $phone;
    public $latitude;
    public $longitude;
    public $whatsapp;
    public $facebook;
    public $instagram;
    public $youtube;
    public $created_at;
    public $type_id;
    public $expired_at;

    private $_oldAttributes;

    public function rules()
    {
        return [
            [['name', 'address', 'phone','facebook', 'instagram', 'youtube', 'latitude', 'longitude', 'whatsapp', 'type_id', 'created_at', 'expired_at'], 'string'],
            [['name', 'address', 'phone'], 'required'],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
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
            'created_at' => 'Дата создания объекта',
            'type_id' => 'Тип объекта',
            'expired_at' => 'Дата окончания лицензии',
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
        $this->created_at = $settings->getCreatedAt();
        $this->type_id = $settings->getTypeId();
        $this->expired_at = $settings->getExpiredAt();

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
         if ($this->_oldAttributes['type_id'] != $this->type_id) {
            $settings->setTypeId($this->type_id);
        }
         if ($this->_oldAttributes['created_at'] != $this->created_at) {
            $settings->setCreatedAt($this->created_at);
        }
         if ($this->_oldAttributes['expired_at'] != $this->expired_at) {
            $settings->setExpiredAt($this->expired_at);
        }
    }

    private function setIsUpdate($key)
    {
        $setting = Setting::findOne(['key' => $key]);
        $setting->is_updated = false;
        return $setting->save();
    }

    public static function getTypes()
    {
        return [
            self::TYPE_SHOP => 'Магазин',
            self::TYPE_RESTAURANT => 'Ресторан'
        ];
    }

    public function getTypeLabel()
    {
        return ArrayHelper::getValue(self::getTypes(), $this->type_id);
    }
}