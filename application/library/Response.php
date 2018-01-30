<?php

/**
 * 业务响应类
 *
 * @Author   liuchao
 *
 * Class Response
 */
class Response {

    /**
     * 成功响应的编码
     */
    const SUCCESS = 200;

    /**
     * 失败响应的编码
     */
    const FAIL = 400;

    /**
     * 未授权
     */
    const UNAUTH = 401;

    /**
     * success
     *
     * @param null   $data
     * @param string $message
     *
     * @return \Base\HttpResponse
     *
     * @author  liuchao
     */
    public static function success($data = null, $message = 'success') {
        return self::json(self::SUCCESS, $data, $message);
    }

    /**
     * fail
     *
     * @param string $message
     * @param null   $data
     *
     * @return \Base\HttpResponse
     *
     * @author  liuchao
     */
    public static function fail($message = 'fail', $data = null) {
        return self::json(self::FAIL, $data, $message);
    }

    /**
     * json 格式响应
     *
     * @param        $errno
     * @param null   $data
     * @param string $message
     *
     * @return \Base\HttpResponse
     *
     * @author  liuchao
     */
    public static function json($errno, $data = null, $message = '') {
        $ret = [
            'errno'     => $errno,
            'errmsg'    => $message,
            'timestamp' => time(),
            'data'      => $data,
        ];

        return new \Base\HttpResponse($ret);
    }

    /**
     * 原生响应
     *
     * @param       $data
     * @param int   $code
     * @param array $headers
     *
     * @return \Base\HttpResponse
     *
     * @author  liuchao
     */
    public static function raw($data, $code = 200, $headers = []) {
        return new \Base\HttpResponse($data, $code, $headers);
    }

}
