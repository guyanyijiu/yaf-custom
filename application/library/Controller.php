<?php

/**
 * Controller 基类
 *
 * @Author   liuchao
 * Class Controller
 */
class Controller extends \Yaf_Controller_Abstract {

    public function init() {

        $action = APP_PATH . '/modules/' . static::getRequest()->module . '/actions/' . static::getRequest()->controller . '/' . static::getRequest()->action . '.php';
        Yaf_Loader::import($action);

        $class = static::getRequest()->action;
        $class = "\\Actions\\$class";
        if ( !class_exists($class)) {
            $class = $class . 'Action';
        }

        $instance = Yaf_Registry::get('container')->make($class);
        $instance->execute();
        exit;
    }

}
