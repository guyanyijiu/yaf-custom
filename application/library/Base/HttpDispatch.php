<?php

namespace Base;

/**
 * 路由分发
 *
 * Class HttpDispatch
 *
 * @package Base
 * @author  liuchao
 */
class HttpDispatch {

    /**
     * 执行路由并输出结果
     *
     * @param $request
     *
     * @author  liuchao
     */
    public function run($request = null) {

        if (is_null($request)) {
            $request = container('Request');
        }

        $response = $this->dispatch($request);

        if ($response instanceof HttpResponse) {
            $response->send();
        } else {
            echo (string) $response;
        }
    }

    /**
     * 执行路由返回响应
     *
     * @param $request
     *
     * @return HttpResponse
     *
     * @author  liuchao
     */
    public function dispatch($request) {

        try {

            $actionPath = APP_PATH . '/modules/' . $request->module . '/actions/' . $request->controller . '/' . $request->action . '.php';

            if ( !\Yaf_Loader::import($actionPath)) {
                throw new \Exceptions\ActionLoadFailedException('Bad URL');
            }

            $class = '\\Actions\\' . $request->action;
            if ( !class_exists($class)) {
                throw new \Exceptions\ActionNotExistException('Action ' . $request->action . ' Not Found');
            }

            return $this->prepareResponse(container()->call($class . '@execute'));

        } catch (\Throwable $e) {
            return \HandleExceptions::makeExceptionResponse($e);
        }

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
        if ( !$response instanceof HttpResponse) {
            $response = new HttpResponse($response);
        }

        return $response;
    }


}