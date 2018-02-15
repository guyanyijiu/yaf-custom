<?php

namespace Middleware;

/**
 * 定义中间件
 *
 * Class Example
 *
 * @package Middleware
 * @author  liuchao
 */
class Example {

    public function handle(\Request $request, \Response $response, $next) {

        // 请求处理前逻辑

        $response = $next($request, $response);

        // 请求处理后逻辑

        return $response;
    }

}