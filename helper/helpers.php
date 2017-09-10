<?php

/**
 *  框架助手函数，快捷的完成各种框架相关的功能
 */


if (! function_exists('container')) {
    /**
     * 获取容器实例
     *
     * @param null $name
     *
     * @return object
     */
    function container($name = null){
        if($name){
            return (Yaf_Registry::get('container'))[$name];
        }
        return Yaf_Registry::get('container');
    }
}

if (! function_exists('config')) {

    /**
     * 获取 配置项
     *
     * @Author   liuchao
     *
     * @param null $key
     * @param null $default
     *
     * @return null
     */
    function config($key = null, $default = null){

        if (is_null($key)) {
            return container('config')->toArray();
        }
        return isset(container('config')[$key]) ? container('config')[$key] : $default;
    }
}


