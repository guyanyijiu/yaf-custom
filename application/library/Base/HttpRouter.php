<?php

namespace Base;

/**
 * 路由执行类
 *
 * Class HttpRouter
 *
 * @package Base
 * @author  liuchao
 */
class HttpRouter {

    use MiddlewareTrait;

    /**
     * @var Container
     */
    private $container;

    /**
     * HttpRouter constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * 执行
     *
     * @param $request
     * @param $response
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function run($request, $response) {
        return $this->callMiddlewareStack($request, $response);
    }

    /**
     * 处理
     *
     * @param \Request  $request
     * @param \Response $response
     *
     * @return HttpResponse|mixed|\Response
     * @throws \Exceptions\ActionLoadFailedException
     * @throws \Exceptions\ActionNotExistException
     *
     * @author  liuchao
     */
    public function __invoke(\Request $request, \Response $response) {

        $actionFile = APP_PATH . '/modules/' . $request->getModule() . '/actions/' . $request->getController() . '/' . $request->getAction() . '.php';

        if ( !\Yaf_Loader::import($actionFile)) {
            throw new \Exceptions\ActionLoadFailedException('Bad URL');
        }

        $action = '\\Actions\\' . $request->action;
        if ( !class_exists($action)) {
            throw new \Exceptions\ActionNotExistException('Action ' . $request->action . ' Not Found');
        }

        $response = $this->container->call($action . '@execute');
        $response = $this->container->prepareResponse($response);

        return $response;
    }

}