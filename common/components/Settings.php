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
        $this->set('object.name', $address);
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
}