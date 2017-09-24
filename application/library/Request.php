<?php

/**
 * 简化请求类的使用
 *
 * @Author   liuchao
 *
 * Class Request
 */

class Request {

    /**
     * 静态方法调用代理
     *
     * @Author   liuchao
     *
     * @param $method
     * @param $parameters
     *
     * @return bool
     */
    public static function __callStatic($method, $parameters){
        $request = Yaf_Dispatcher::getInstance()->getRequest();
        switch ($method){
            case 'get':
                return $request->getQuery(...$parameters);
            case 'post':
                return $request->getPost(...$parameters);
            case 'request':
                return $request->getRequest(...$parameters);
            case 'find':
                return $request->get(...$parameters);
            case 'cookie':
                return $request->getCookie(...$parameters);
            case 'file':
                return $request->getFiles(...$parameters);
            case 'isAjax':
                return $request->isXmlHttpRequest();
        }
        return $request->$method(...$parameters);
    }

}
