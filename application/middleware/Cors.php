<?php

namespace Middleware;

/**
 * 跨域支持
 *
 * Class Cors
 *
 * @package Middleware
 * @author  liuchao
 */
class Cors {

    public function handle(\Request $request, \Response $response, $next) {

        $response->setHeaders([
            'Access-Control-Allow-Origin'  => '*', // 生产环境为了安全，这里配置具体域名
            'Access-Control-Allow-Methods' => 'GET,POST,HEAD,OPTIONS',
            'Access-Control-Allow-Headers' => 'Authorization',
        ]);

        // 对于预检请求，响应空即可
        if ($request->getMethod() == 'OPTIONS') {
            return $response;
        }

        return $next($request, $response);
    }
}