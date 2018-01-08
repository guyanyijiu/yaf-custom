<?php

/**
 * 跨模块调用
 *
 * Class Rpc
 *
 * @author  liuchao
 */
class Rpc {

    /**
     * GET 调用
     *
     * @param       $url
     * @param array $query
     * @param array $args
     *
     * @return string
     *
     * @author  liuchao
     */
    public static function get($url, array $query = [], $args = []) {
        $server = isset($args['server']) ? $args['server'] : [];
        $headers = isset($args['headers']) ? $args['headers'] : [];

        return static::dispatch('GET', $url, $query, $server);
    }

    /**
     * POST 调用
     *
     * @param        $url
     * @param array  $query
     * @param string $body
     * @param array  $args
     *
     * @return string
     *
     * @author  liuchao
     */
    public static function post($url, array $query = [], $body = 'form', $args = []) {
        $server = isset($args['server']) ? $args['server'] : [];
        $headers = isset($args['headers']) ? $args['headers'] : [];
        $content = null;
        if ($body == 'json') {
            $content = json_encode($query, JSON_UNESCAPED_UNICODE);
            $query = [];
        }

        return static::dispatch('POST', $url, $query, $server, $content);
    }

    /**
     * 路由
     *
     * @param       $method
     * @param       $uri
     * @param       $parameters
     * @param array $server
     * @param null  $content
     *
     * @return string
     *
     * @author  liuchao
     */
    private static function dispatch($method, $uri, $parameters, $server = [], $content = null) {

        $originalRequest = container('Request');

        try {
            $request = (new \Base\HttpRequest())->create($uri, $method, $parameters, $server, $content);
            $request = new \Request($request);
            container()->instance('Request', $request);

            $response = (new \Base\HttpDispatch())->dispatch($request);

        } catch (\Throwable $e) {
            $response = \HandleExceptions::makeExceptionResponse($e);
        }

        container()->instance('Request', $originalRequest);

        return $response->getContent();
    }


}