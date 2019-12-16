<?php

/**
 * @link http://phe.me
 * @copyright Copyright (c) 2014 Pheme
 * @license MIT http://opensource.org/licenses/MIT
 */

namespace common\components;

use Exception;
use pheme\settings\components\Settings as BaseSettings;

class Settings extends BaseSettings
{
    public function setBalance($new_value, $isMinus = false)
    {
        if ($isMinus)
            $new_value *= -1;

        $value = $this->get('object.balance');
        $value += $new_value;
        return $this->set('object.balance', $value);
    }

    public function getBalance()
    {
        return $this->get('object.balance');
    }

    public function setName($name)
    {
        $this->set('object.name', $name, null, 'string');
    }

    public function getName()
    {
        return $this->get('object.name');
    }

    public function setAddress($address)
    {
        $this->set('object.address', $address, null, 'string');
    }

    public function getAddress()
    {
        return $this->get('object.address');
    }

    public function setPhone($phone)
    {
        $this->set('object.phone', $phone, null, 'string');
    }

    public function getPhone()
    {
        return $this->get('object.phone');
    }

    public function setToken($token)
    {
        $this->set('object.token', $token, null, 'string');
    }

    public function getToken()
    {
        return $this->get('object.token');
    }

    public function setSerialNumber($token)
    {
        $this->set('object.serial_number', $token, null, 'string');
    }

    public function getSerialNumber()
    {
        return $this->get('object.serial_number');
    }

    public function setLongitude($longitude)
    {
        $this->set('object.longitude', $longitude, null, 'string');
    }

    public function getLongitude()
    {
        return $this->get('object.longitude');
    }

    public function setLatitude($latitude)
    {
        $this->set('object.latitude', $latitude, null, 'string');
    }

    public function getLatitude()
    {
        return $this->get('object.latitude');
    }

    public function setWhatsapp($whatsapp)
    {
        $this->set('object.whatsapp', $whatsapp, null, 'string');
    }

    public function getWhatsapp()
    {
        return $this->get('object.whatsapp');
    }

    public function setFacebook($facebook)
    {
        $this->set('object.facebook', $facebook, null, 'string');
    }

    public function getFacebook()
    {
        return $this->get('object.facebook');
    }

    public function setInstagram($instagram)
    {
        $this->set('object.instagram', $instagram, null, 'string');
    }

    public function getInstagram()
    {
        return $this->get('object.instagram');
    }

    public function setYoutube($youtube)
    {
        $this->set('object.youtube', $youtube, null, 'string');
    }

    public function getYoutube()
    {
        return $this->get('object.youtube');
    }

    public function getTypeId()
    {
        return $this->get('object.type_id');
    }

    public function getCreatedAt()
    {
        return $this->get('object.created_at');
    }
}