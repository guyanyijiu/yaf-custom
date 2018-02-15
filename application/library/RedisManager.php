<?php

/**
 * Redis 操作类
 *
 * Class RedisManager
 *
 * @author  liuchao
 */
class RedisManager {

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