<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MenuAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css?family=Alegreya+Sans+SC&display=swap',
        'css/site.css',
        'css/font-awesome.min.css',
        'css/bootstrap.min.css',
        'css/bootstrap-theme.min.css',
        'css/menu.css'
    ];
    public $js = [
        'js/bootstrap.min.js',
        'js/ymap.js',
        'js/common.js',
        'js/vue.js',
        'js/menu.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
