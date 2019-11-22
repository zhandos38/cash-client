<?php
/**
 * Created by PhpStorm.
 * User: Kami
 * Date: 14.03.2019
 * Time: 16:54
 */

namespace common\components\auth;


class CustomHttpBearerAuth extends CustomHttpAuthMethod
{
    /**
     * {@inheritdoc}
     */
    public $header = 'Authorization';
    /**
     * {@inheritdoc}
     */
    public $pattern = '/^Bearer\s+(.*?)$/';
    /**
     * @var string the HTTP authentication realm
     */
    public $realm = 'api';


    /**
     * {@inheritdoc}
     */
    public function challenge($response)
    {
        $response->getHeaders()->set('WWW-Authenticate', "Bearer realm=\"{$this->realm}\"");
    }
}