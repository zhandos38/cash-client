<?php


use yii\web\UrlNormalizer;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app - api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module'
        ]
    ],
'components' => [
    'request' => [
        'enableCookieValidation' => false,
        'parsers' => [
            'application/json' => 'yii\web\JsonParser',
        ]
    ],
    'user' => [
        'identityClass' => 'common\models\User',
        'enableAutoLogin' => false,
        'enableSession' => false,
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
            ],
        ],
    ],
    'urlManager' => [
        'enablePrettyUrl' => true,
        'enableStrictParsing' => true,
        'showScriptName' => false,
        'rules' => [
            '' => 'site/index',
            'GET activate' => 'v1/object/activate'
        ]
    ]
],
'params' => $params,
];