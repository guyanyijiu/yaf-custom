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
            \Yaf_Loader::import($actionPath);

            $class = '\\Actions\\' . $request->action;

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