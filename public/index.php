<?php

/**
 * 入口文件
 */
define("ROOT_PATH",  realpath(dirname(__FILE__) . '/../'));
$app  = new Yaf_Application(ROOT_PATH . "/conf/application.ini");
$app->bootstrap()->run();
