<?php

/**
 * 入口文件
 */

define("APP_PATH",  realpath(dirname(__FILE__) . '/../'));
$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");
$app->bootstrap()->run();