<?php

/**
 *  框架助手函数，快捷的完成各种框架相关的功能
 */


if ( !function_exists('container')) {
    /**
     * 获取容器实例
     *
     * @param null $name
     *
     * @return mixed|\Base\Container
     *
     * @author  liuchao
     */
    function container($name = null) {
        if ($name) {
            return Yaf_Registry::get('container')->make($name);
        }

        return Yaf_Registry::get('container');
    }
}

if ( !function_exists('config')) {

    /**
     * 获取 配置项
     *
     * @param null $key
     * @param null $default
     *
     * @return mixed
     *
     * @author  liuchao
     */
    function config($key = null, $default = null) {

        if (is_null($key)) {
            return container('config')->toArray();
        }

        return container('config')->get($key, $default);
    }
}

if ( !function_exists('event')) {

    /**
     * 触发事件并调用监听者
     *
     * @param       $event
     * @param array $payload
     * @param bool  $halt
     *
     * @return mixed
     *
     * @author  liuchao
     */
    function event($event, $payload = [], $halt = false) {
        return container('events')->fire($event, $payload, $halt);
    }
}


