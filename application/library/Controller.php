<?php

/**
 * Controller 基类
 *
 * @Author   liuchao
 * Class Controller
 */
class Controller extends \Yaf_Controller_Abstract {

    public function init(){

        $this->actions[static::getRequest()->action] = 'modules/' . static::getRequest()->module . '/actions/' . static::getRequest()->controller . '/' . static::getRequest()->action . '.php';

    }
}
