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

        return static::dispatch('GET', $url, $query, [], $server);
    }

    /**
     * POST 调用
     *
     * @param        $url
     * @param array  $data
     * @param string $body
     * @param array  $args
     *
     * @return string
     *
     * @author  liuchao
     */
    public static function post($url, array $data = [], $body = 'form', $args = []) {
        $server = isset($args['server']) ? $args['server'] : [];
        $headers = isset($args['headers']) ? $args['headers'] : [];
        $content = null;
        if ($body == 'json') {
            $content = json_encode($data, JSON_UNESCAPED_UNICODE);
            $data = [];
        }

        return static::dispatch('POST', $url, [], $data, $server, $content);
    }

    /**
     * 执行
     *
     * @param       $method
     * @param       $uri
     * @param array $get
     * @param array $post
     * @param array $server
     * @param null  $content
     *
     * @return string
     *
     * @author  liuchao
     */
    private static function dispatch($method, $uri, $get = [], $post = [], $server = [], $content = null) {

        try {
            $container = container();

            $originalRequest = $container->make(Request::class);
            $originalResponse = $container->make(Response::class);

            $request = new Request($uri, $method, $get, $post, $server, $content);
            $response = new Response();

            $container->instance(Request::class, $request);
            $container->instance(Response::class, $response);

            $response = $container->process($request, $response);

            $container->instance(Request::class, $originalRequest);
            $container->instance(Response::class, $originalResponse);

            return $response->getContent();
        } catch (\Throwable $e) {
            return \HandleExceptions::makeExceptionResponse($e)->getContent();
        }

    }


}