<?php

use Illuminate\Container\Container;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Connectors\ConnectionFactory;

class DB {

    /**
     * @var array
     */
    protected static $resolver = [];

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    protected $manager;

    /**
     * @var \DB
     */
    protected static $instance;

    /**
     * DBM constructor.
     *
     * @param Container|null $container
     */
    public function __construct(Container $container = null) {
        $this->setupContainer($container ?: new Container);

        $this->setupManager();
    }

    /**
     * 设置容器
     *
     * @param Container $container
     *
     * @author  liuchao
     */
    protected function setupContainer(Container $container) {
        $this->container = $container;
    }

    /**
     * 设置 Database Manager
     *
     *
     * @author  liuchao
     */
    protected function setupManager() {
        $factory = new ConnectionFactory($this->container);

        $this->manager = new DatabaseManager($this->container, $factory);
    }

    /**
     * 获取 DB 实例
     *
     * @return DB
     *
     * @author  liuchao
     */
    public static function instance() {
        if (is_null(static::$instance)) {
            static::$instance = container('db');
        }

        return static::$instance;
    }

    /**
     * 获取一个连接实例
     *
     * @param null $connection
     *
     * @return \Illuminate\Database\Connection
     *
     * @author  liuchao
     */
    public static function connection($connection = null) {
        return static::instance()->getConnection($connection);
    }

    /**
     * 获取一个查询构造器
     *
     * @param      $table
     * @param null $connection
     *
     * @return \Illuminate\Database\Query\Builder
     *
     * @author  liuchao
     */
    public static function table($table, $connection = null) {
        return static::connection($connection)->table($table);
    }

    /**
     * 获取一个 schema 构造器实例
     *
     * @param null $connection
     *
     * @return \Illuminate\Database\Schema\Builder
     *
     * @author  liuchao
     */
    public static function schema($connection = null) {
        return static::connection($connection)->getSchemaBuilder();
    }

    /**
     * 获取连接实例
     *
     * @param null $name
     *
     * @return \Illuminate\Database\Connection
     *
     * @author  liuchao
     */
    public function getConnection($name = null) {
        if (is_null($name)) {
            $name = config('database.default');
        }

        $connection = $this->manager->connection($name);

        if ( !isset(static::$resolver[$name])) {
            $this->enableQueryLog($connection);
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
    protected function enableQueryLog(\Illuminate\Database\Connection $connection) {
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

    /**
     * 代理静态方法调用
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public static function __callStatic($method, $parameters) {
        return static::connection()->$method(...$parameters);
    }

}
