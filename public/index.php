<?php

/**
 * 入口文件
 */

// 记录请求开始时间
define('YAF_START', microtime(true));

// 项目根目录
define("ROOT_PATH",  realpath(dirname(__FILE__) . '/../'));

$app  = new Yaf_Application(ROOT_PATH . "/conf/application.ini");

$app->bootstrap()->run();
