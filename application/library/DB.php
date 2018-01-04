<?php

/**
 * DB操作类
 *
 * Class DB
 *
 * @author  liuchao
 */
class DB {

    /**
     * 已经建立的连接
     *
     * @var array
     */
    private static $resolver = [];

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
    public static function __callStatic($method, $parameters) {
        return static::connection()->$method(...$parameters);
    }

    /**
     * 获取数据库连接实例
     *
     * @param null $name
     *
     * @return \Illuminate\Database\Connection
     *
     * @author  liuchao
     */
    public static function connection($name = null) {
        if (is_null($name)) {
            $name = config('database.default');
        }

        $connection = container('db')->getConnection($name);
        if ( !isset(static::$resolver[$name])) {
            static::enableQueryLog($connection);
            static::$resolver[$name] = true;
        }
        return $connection;
    }

    /**
     * 启用SQL日志记录
     *
     * @param \Illuminate\Database\Connection $connection
     *
     * @author  liuchao
     */
    private static function enableQueryLog(\Illuminate\Database\Connection $connection) {
        if (PHP_SAPI == 'cli') {
            $connection->listen(function ($query) {
                \Log::sql([['query' => $query->sql, 'bindings' => $query->bindings, 'time' => $query->time]]);
            });
        } else {
            $connection->enableQueryLog();
            register_shutdown_function(function ($connection) {
                \Log::sql($connection->getQueryLog());
            }, $connection);
        }
    }

}
