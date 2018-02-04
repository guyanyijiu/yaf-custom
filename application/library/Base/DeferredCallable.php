<?php

namespace Base;

/**
 * 函数延迟调用类
 *
 * Class DeferredCallable
 *
 * @package Base
 * @author  liuchao
 */
class DeferredCallable {

    /**
     * @var mixed
     */
    private $callable;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    protected $method = 'handle';

    /**
     * DeferredCallable constructor.
     *
     * @param                $callable
     * @param Container|null $container
     */
    public function __construct($callable, Container $container = null) {
        $this->callable = $callable;
        $this->container = $container;
    }

    /**
     * 处理 callback
     *
     * @param $callable
     *
     * @return \Closure|mixed
     *
     * @author  liuchao
     */
    protected function resolveCallable($callable) {
        if ($callable instanceof \Closure) {
            return $callable->bindTo($this->container);
        } elseif ( !is_object($callable)) {
            return $this->container->make($callable);
        }

        return $callable;
    }

    /**
     * 实际调用执行
     *
     * @param array ...$parameters
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function __invoke(...$parameters) {
        $callable = $this->resolveCallable($this->callable);

        return method_exists($callable, $this->method)
            ? $callable->{$this->method}(...$parameters)
            : $callable(...$parameters);
    }
}
