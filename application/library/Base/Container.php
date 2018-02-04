<?php

namespace Base;

use Illuminate\Container\Container as IlluminateContainer;

/**
 * 基础容器类
 *
 * Class Container
 *
 * @package Base
 * @author  liuchao
 */
class Container extends IlluminateContainer {

    use MiddlewareTrait;

    /**
     * @var HttpRouter
     */
    public $router;

    /**
     * Container constructor.
     */
    public function __construct() {
        static::setInstance($this);
        $this->router = new HttpRouter($this);
    }

    /**
     * 执行应用
     *
     * @param \Request|null  $request
     * @param \Response|null $response
     *
     * @author  liuchao
     */
    public function run(\Request $request = null, \Response $response = null) {
        if(is_null($request)){
            $request = $this->resolve(\Request::class);
        }
        if(is_null($response)){
            $response = $this->resolve(\Response::class);
        }

        $response = $this->process($request, $response);

        if ($response instanceof \Response) {
            $response->send();
        } else {
            echo (string) $response;
        }
    }

    /**
     * 处理请求
     *
     * @param \Request  $request
     * @param \Response $response
     *
     * @return HttpResponse|\Response
     *
     * @author  liuchao
     */
    public function process(\Request $request, \Response $response) {

        try {
            $response = $this->callMiddlewareStack($request, $response);
        } catch (\Throwable $e) {
            $response = \HandleExceptions::makeExceptionResponse($e);
        }

        $response = $this->prepareResponse($response);

        return $response;
    }

    /**
     * 处理返回值
     *
     * @param $response
     *
     * @return HttpResponse
     *
     * @author  liuchao
     */
    public function prepareResponse($response) {
        if ( !$response instanceof \Response) {
            $response = new \Response($response);
        }

        return $response;
    }

    /**
     * 注册中间件
     *
     * @param $middleware
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function middleware($middleware) {
        if (is_array($middleware)) {
            foreach ($middleware as $v) {
                $this->addMiddleware(new DeferredCallable($v, $this));
            }

            return $this;
        }

        return $this->addMiddleware(new DeferredCallable($middleware, $this));
    }

    /**
     * 执行请求
     *
     * @param \Request  $request
     * @param \Response $response
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function __invoke(\Request $request, \Response $response) {
        return $this->router->run($request, $response);
    }

}