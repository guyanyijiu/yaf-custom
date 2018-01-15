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
     * 错误信息
     *
     * @var string
     */
    protected $error;

    /**
     * 非查询构造器方法
     *
     * @var array
     */
    protected $support = [
        'raw', 'transaction', 'beginTransaction', 'rollBack', 'commit',
    ];

    /**
     * Model constructor.
     *
     * @param null $table
     * @param null $connection
     */
    public function __construct($table = null, $connection = null) {

        if ( !is_null($table)) {
            $this->table = $table;
        }
        if ( !is_null($connection)) {
            $this->connection = $connection;
        }

        if (is_null($this->table)) {
            $this->table = snake_case(class_basename(static::class));
        }
    }

    /**
     * 获取连接
     *
     * @param null $name
     *
     * @return \Illuminate\Database\Connection
     *
     * @author  liuchao
     */
    public function connection($name = null) {
        if (is_null($name)) {
            $name = $this->connection;
        }

        return \DB::connection($name);
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
            if (in_array($method, $this->support)) {
                return $this->connection($this->connection)->$method(...$parameters);
            }

            return $this->connection($this->connection)->table($this->table)->$method(...$parameters);
        } catch (\BadMethodCallException $e) {
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', get_class($this), $method)
            );
        }
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