<?php

/**
 * DB 操作类
 *
 * @Author   liuchao
 * Class DB
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
        return container('db')->$method(...$parameters);
    }

}
