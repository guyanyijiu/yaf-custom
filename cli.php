<?php
//declare(ticks=1);
ini_set('memory_limit', -1);
// 记录请求开始时间
define('YAF_START', microtime(true));
define("ROOT_PATH",  __DIR__);

require ROOT_PATH . '/vendor/autoload.php';

$app  = new Yaf_Application(ROOT_PATH . "/conf/application.ini");

$app->bootstrap();
