<?php

/**
 * 因 Redis 已被PHP扩展定义，故用名 LRdis
 *
 * Class LRedis
 *
 * @author  liuchao
 */
class LRedis {

    protected static $instance;

    protected static function resolveInstance() {
        if ( !isset(static::$instance)) {
            static::$instance = container('redis');
        }

        return static::$instance;
    }

    public static function __callStatic($method, $parameters) {
        return static::resolveInstance()->$method(...$parameters);
    }

}