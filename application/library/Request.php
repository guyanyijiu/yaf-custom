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
     * 获取原生请求数据
     *
     * @return bool|string
     *
     * @author  liuchao
     */
    public static function raw() {
        return file_get_contents("php://input");
    }

    /**
     * 获取 get 数据
     *
     * @param array ...$params
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public static function get(...$params) {
        return Yaf_Dispatcher::getInstance()->getRequest()->getQuery(...$params);
    }

    /**
     * 获取 post 数据
     *
     * @param array ...$params
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public static function post(...$params) {
        return Yaf_Dispatcher::getInstance()->getRequest()->getPost(...$params);
    }

    /**
     * 获取 header 数据
     *
     * @param null $key
     * @param null $default
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public static function header($key = null, $default = null) {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$name] = $value;
            } elseif ($name == "CONTENT_TYPE") {
                $headers["Content-Type"] = $value;
            } elseif ($name == "CONTENT_LENGTH") {
                $headers["Content-Length"] = $value;
            }
        }
        if (is_null($key)) {
            return $headers;
        }

        return isset($headers[$key]) ? $headers[$key] : $default;
    }

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
    public static function __callStatic($method, $parameters) {
        $request = Yaf_Dispatcher::getInstance()->getRequest();
        switch ($method) {
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
