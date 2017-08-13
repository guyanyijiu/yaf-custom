<?php

namespace Controller;

/**
 * api模块的控制器基类
 *
 * @Author   liuchao
 * Class Api
 * @package  Controller
 */
class Api extends \Controller {
    /**
     * 构造方法
     *
     * @Author   liuchao
     */
    public function init(){
        parent::init();

        /**
         * 禁用自动渲染
         */
        \Yaf_Dispatcher::getInstance()->autoRender(false);
    }
}
