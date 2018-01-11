<?php

class Queue {


    protected $container;

    /**
     * 连接
     *
     * @var string
     */
    protected static $connections = [];

    /**
     * Queue constructor.
     */
    public function __construct() {
        $this->container = Yaf_Registry::get('container');
    }

    public static function onConnection($name = null) {
        return (new static)->getConnection($name);
    }

    /**
     * 获取队列连接
     *
     * @param null $name
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function getConnection($name = null) {
        $name = $name ?: $this->getDefaultDriver();
        if ( !isset(static::$connections[$name])) {
            static::$connections[$name] = $this->resolve($name);
        }

        return static::$connections[$name];
    }

    /**
     * 获取默认队列驱动
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function getDefaultDriver() {
        return $this->container['config']['queue.default'];
    }

    /**
     * 获取队列连接实例
     *
     * @param $name
     *
     * @return \Queue\QueueInterface
     *
     * @author  liuchao
     */
    public function resolve($name) {
        $config = $this->container['config']["queue.connections.$name"];

        if ( !$config) {
            throw new InvalidArgumentException("[$name] config is empty");
        }
        switch ($config['driver']) {
            case 'beanstalkd':
                return new Queue\Beanstalkd($config);
            case 'rabbitmq':
                return new \Queue\Rabbitmq($config);
        }

        throw new InvalidArgumentException("Unsupported driver [$name]");
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
            return $this->getConnection()->$method(...$parameters);
        } catch (BadMethodCallException $e) {
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