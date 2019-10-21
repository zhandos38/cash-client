<?php

/**
 * @link http://phe.me
 * @copyright Copyright (c) 2014 Pheme
 * @license MIT http://opensource.org/licenses/MIT
 */

namespace frontend\components;

use Exception;
use pheme\settings\components\Settings as BaseSettings;

class Settings extends BaseSettings
{
    public function setBalance($new_value, $isMinus = false)
    {
        if (!$new_value > 0)
            throw new Exception('Invoice balance error!');

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
}