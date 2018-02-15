<?php

ini_set('memory_limit', '2048M');
date_default_timezone_set('PRC');

// 记录请求开始时间
define('YAF_START', microtime(true));

// 项目根目录
define("ROOT_PATH", __DIR__);
define("APP_PATH", ROOT_PATH . '/application');

// 根据ini配置加载项目配置文件目录
$conf_path = ini_get('qx_partner.mark');
$conf_path = $conf_path ? 'conf/' . $conf_path : 'conf';
define("CONF_PATH", ROOT_PATH . '/' . $conf_path);

require ROOT_PATH . '/vendor/autoload.php';
require APP_PATH . '/helpers.php';

// 注册异常处理
\HandleExceptions::register();

// 实例化一个容器对象
$container = new \Illuminate\Container\Container();

// 注册config
$container->singleton('config', function () {
    return new \Config(CONF_PATH);
});

// 注册events
$container->singleton('events', function ($container) {
    return new \Illuminate\Events\Dispatcher($container);
});

// 注册db
$container->singleton('db', function ($container) {
    return new \DB($container);
});

// 注册 redis
$container->singleton('redis', function ($container) {
    $config = $container->make('config')->get('database.redis');
    $driver = $config['client'];
    unset($config['client']);

    return new \Illuminate\Redis\RedisManager($driver, $config);
});

Yaf_Registry::set('container', $container);