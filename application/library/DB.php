<?php

/**
 * @mixin \Illuminate\Database\Capsule\Manager
 *
 * Class DB
 *
 * @author  liuchao
 */
class DB {

    /**
     * 静态方法调用代理
     *
     * @Author   liuchao
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters){
        return (Yaf_Registry::get('container')['db'])::$method(...$parameters);
    }

}
