<?php

ini_set('memory_limit', '2048M');
date_default_timezone_set('PRC');

// 记录请求开始时间
define('YAF_START', microtime(true));
define("ROOT_PATH",  __DIR__);

if(YAF_ENVIRON == 'product'){
    error_reporting(0);
}else{
    error_reporting(E_ALL);
}

require ROOT_PATH . '/vendor/autoload.php';
require ROOT_PATH . '/helper/functions.php';
require ROOT_PATH . '/helper/helpers.php';

// 实例化一个容器对象
$container = new \Illuminate\Container\Container();

// 注册config
$container['config'] = function (){
    return new Config();
};

// 注册db
$container->singleton('db', function($container){
    $db = new \Illuminate\Database\Capsule\Manager($container);
    $db->setAsGlobal();
    return $db;
});

Yaf_Registry::set('container', $container);