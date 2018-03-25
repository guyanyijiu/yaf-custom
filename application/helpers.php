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

if ( !function_exists('view')) {

    /**
     * 简易的视图实现
     *
     * @param null  $view
     * @param array $data
     *
     * @return string
     * @throws Throwable
     *
     * @author  liuchao
     */
    function view($view = null, $data = []) {

        $path = config('view.paths') . '/' . str_replace('.', '/', $view) . '.html';

        $obLevel = ob_get_level();

        ob_start();

        extract($data, EXTR_SKIP);

        try {
            include $path;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw $e;
        }

        return ltrim(ob_get_clean());
    }
}

if ( !function_exists('trans')) {
    /**
     * 多语言支持
     *
     * @param null  $id
     * @param array $replace
     * @param null  $locale
     *
     * @return \Base\Container|mixed
     *
     * @author  liuchao
     */
    function trans($id = null, $replace = [], $locale = null) {
        if (is_null($id)) {
            return container('translator');
        }

        return container('translator')->trans($id, $replace, $locale);
    }
}

if ( !function_exists('validate_trans')) {
    /**
     * 多语言版本校验类
     *
     * @param array $rules
     *
     * @return Validate
     *
     * @author  liuchao
     */
    function validate_trans(array $rules) {
        $messages = container('translator')->trans('validation');
        $attributes = container('translator')->trans('validation.attributes');
        if ( !is_array($attributes)) {
            $attributes = [];
        }

        return new \Validate($rules, $messages, $attributes);
    }
}

