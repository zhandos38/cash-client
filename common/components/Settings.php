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
        $this->set('object.name', $name);
    }

    public function setAddress($address)
    {
        $this->set('object.address', $address);
    }

    public function setPhone($phone)
    {
        $this->set('object.phone', $phone);
    }

    public function setToken($token)
    {
        $this->set('object.token', $token);
    }

    public function getToken()
    {
        return $this->get('object.token');
    }

    public function setSerialNumber($token)
    {
        $this->set('object.serial_number', $token);
    }

    public function getSerialNumber()
    {
        return $this->get('object.serial_number');
    }

    public function setLongitude($longitude)
    {
        $this->set('object.longitude', $longitude);
    }

    public function getLongitude()
    {
        $this->get('object.longitude');
    }

    public function setLatitude($latitude)
    {
        $this->set('object.latitude', $latitude);
    }

    public function getLatitude()
    {
        $this->get('object.latitude');
    }

    public function setWhatsapp($whatsapp)
    {
        $this->set('object.whatsapp', $whatsapp);
    }

    public function getWhatsapp($whatsapp)
    {
        $this->get('object.whatsapp');
    }

    public function setFacebook($facebook)
    {
        $this->set('object.facebook', $facebook);
    }

    public function getFacebook($facebook)
    {
        $this->get('object.facebook');
    }

    public function setInstagram($instagram)
    {
        $this->set('object.instagram', $instagram);
    }

    public function getInstagram($instagram)
    {
        $this->get('object.instagram');
    }

    public function setYoutube($youtube)
    {
        $this->set('object.youtube', $youtube);
    }

    public function getYoutube($youtube)
    {
        $this->get('object.youtube');
    }
}