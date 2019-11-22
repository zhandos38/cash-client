<?php
namespace common\components\auth;
use yii\helpers\VarDumper;

/**
 * Created by PhpStorm.
 * User: Kami
 * Date: 14.03.2019
 * Time: 16:51
 */

class CustomHttpAuthMethod extends \yii\filters\auth\AuthMethod
{

    /**
     * @var string the HTTP header name
     */
    public $header = 'X-Api-Key';
    /**
     * @var string a pattern to use to extract the HTTP authentication value
     */
    public $pattern;

    /**
     * Authenticates the current user.
     * @param \yii\web\User $user
     * @param \yii\web\Request $request
     * @param \yii\web\Response $response
     * @return \yii\web\IdentityInterface the authenticated user identity. If authentication information is not provided, null will be returned.
     * @throws \yii\web\UnauthorizedHttpException if authentication information is provided but is invalid.
     */
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get($this->header);

        if ($authHeader !== null) {
            if ($this->pattern !== null) {
                if (preg_match($this->pattern, $authHeader, $matches)) {
                    $authHeader = $matches[1];
                } else {
                    return null;
                }
            }

            $identity = $user->loginByAccessToken($authHeader, get_class($this));



            if ($identity === null) {
                $this->challenge($response);
                $this->handleFailure($response);
            }

            return $identity;
        }

        return true;
    }
}