<?php

/**
 *  框架助手函数，快捷的完成各种框架相关的功能
 */


if (! function_exists('app')) {
    /**
     * 获取app实例
     * @return object
     */
    function app(){
        return Yaf_Application::app();
    }
}

if (! function_exists('config')) {
    /**
     * 获取/设置配置项
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function config($key = null, $default = null){

        $configs = Yaf_Registry::get('configs');

        if (is_null($key)) {
            return $configs;
        }

        if(strpos($key, '.')) {
            $key = explode('.', $key);
            return array_reduce($key, function($carry, $item) {
                if(isset($carry[$item])){
                    return $carry[$item];
                }else{
                    return null;
                }
            }, $configs);
        }

        return isset($configs[$key]) ? $configs[$key] : null;
    }
}


