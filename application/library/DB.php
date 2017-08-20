<?php

/**
 * DB 操作类，提供类似laravel中DB facade的用法
 *
 * @Author   liuchao
 * Class DB
 */
class DB {

    /**
     * @var array 已经添加的数据库连接
     */
    private static $connections = [];

    /**
     * 获取一个指定连接的Capsule实例
     *
     * @Author   liuchao
     *
     * @param string $name
     *
     * @return mixed
     * @throws \Exception
     */
    public static function connection($name = 'default'){

        $capsule = Yaf_Registry::get('Capsule');

        if(in_array($name, self::$connections)){
            return $capsule->connection($name);
        }

        if($config = config("database.$name")){
            $connections[] = $name;
            $capsule->addConnection($config, $name);
            return $capsule->connection($name);
        }

        throw new Exception('连接不存在');
    }

    /**
     * 代理静态方法调用
     *
     * @Author   liuchao
     *
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args){

        return call_user_func_array("Illuminate\Database\Capsule\Manager::$method", $args);
    }
}
