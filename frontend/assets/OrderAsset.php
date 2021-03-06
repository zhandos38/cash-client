<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class OrderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/order.css',
        'css/jquery.bootstrap-touchspin.css',
        'css/keyboard.css',
        'css/loading.css'
    ];
    public $js = [
        'js/jquery.scannerdetection.js',
        'js/jquery-barcode.min.js',
        'js/jquery.bootstrap-touchspin.js',
        'js/keyboard.js',
        'js/keyboard-layouts.js',
        'js/masonry.js',
        'js/loading.js',
        'js/vue.js',
        'js/order.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
