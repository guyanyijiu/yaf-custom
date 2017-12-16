<?php

class Model {

    /**
     * 连接名
     *
     * @var string
     */
    protected $connection;

    /**
     * 表名
     *
     * @var string
     */
    protected $table;

    /**
     * db 实例
     *
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected static $resolver;


    /**
     * 错误信息
     *
     * @var string
     */
    protected $error;

    /**
     * Model constructor.
     *
     * @param null $table
     * @param null $connection
     */
    public function __construct($table = null, $connection = null) {

        if(!is_null($table)){
            $this->table = $table;
        }
        if(!is_null($connection)){
            $this->connection = $connection;
        }

        if(is_null($this->table)){
            $this->table = snake_case(class_basename(static::class));
        }
    }

    /**
     * 获取一个查询构造器
     *
     * @return \Illuminate\Database\Capsule\Manager
     *
     * @author  liuchao
     */
    public function newQuery() {
        if (is_null(self::$resolver)) {
            self::$resolver = Yaf_Registry::get('container')['db'];
        }

        return (self::$resolver)::table($this->table, $this->connection);
    }

    /**
     * 设置错误信息
     *
     * @param $error
     *
     * @author  liuchao
     */
    public function setError($error) {
        $this->error = $error;
    }

    /**
     * 获取错误信息
     *
     * @return string
     *
     * @author  liuchao
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 代理方法调用
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function __call($method, $parameters) {
        try {
            return $this->newQuery()->$method(...$parameters);
        } catch (\BadMethodCallException $e) {
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', get_class($this), $method)
            );
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
        return (new static)->$method(...$parameters);
    }

}