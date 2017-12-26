<?php

/**
 * 入口文件
 */

// 记录请求开始时间
define('YAF_START', microtime(true));

// 项目根目录
define("ROOT_PATH",  realpath(dirname(__FILE__) . '/../'));

// APP目录
define("APP_PATH", ROOT_PATH . '/application');

// 根据ini配置加载项目配置文件目录
$conf_path = ini_get('qx_partner.mark');
$conf_path = $conf_path ? 'conf/' . $conf_path : 'conf';
define("CONF_PATH", ROOT_PATH . '/' . $conf_path);

$app  = new Yaf_Application(CONF_PATH . "/application.ini");

$app->bootstrap()->run();
