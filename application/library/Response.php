<?php

/**
 * 响应类
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
     * http 响应码
     *
     * @var array
     */
    private static $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /**
     * 返回一个自定义 http 状态码的响应
     *
     * @Author   liuchao
     *
     * @param int   $code
     * @param array $headers
     */
    public static function http($code = 200, $headers = []){
        $protocol =  Yaf_Dispatcher::getInstance()->getRequest()->getServer('SERVER_PROTOCOL', 'HTTP/1.1');
        $string = isset(self::$phrases[$code]) ? self::$phrases[$code] : '';
        header("$protocol $code $string");
        if($headers){
            foreach($headers as $k => $v){
                header($k . ': ' . $v, true);
            }
        }
    }

    /**
     * 成功响应
     *
     * @Author   liuchao
     *
     * @param array  $data      数据
     * @param string $message   提示信息
     */
    public static function success($data = null, $message = 'success'){
        self::json(self::SUCCESS, $data, $message);
    }

    /**
     * 失败响应
     *
     * @Author   liuchao
     *
     * @param array  $data      数据
     * @param string $message   提示信息
     */
    public static function fail($message = 'fail', $data = null){
        self::json(self::FAIL, $data, $message);
    }

    /**
     * json 格式响应
     *
     * @Author   liuchao
     *
     * @param        $errno     响应编码
     * @param array  $data      数据
     * @param string $message   提示信息
     */
    public static function json($errno, $data = null, $message = ''){
        $ret = [
            'errno' => $errno,
            'errmsg' => $message,
            'timestamp' => time(),
            'data' => $data
        ];
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($ret, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * jsonp 的响应
     *
     * @Author   liuchao
     *
     * @param        $errno     响应编码
     * @param array  $data      数据
     * @param string $message   提示信息
     */
    public static function jsonp($errno, $data = [], $message = ''){
        $callback = Request::get('callback');
        $ret = [
            'errno' => $errno,
            'errmsg' => $message,
            'timestamp' => time(),
            'data' => $data
        ];
        header('Content-Type: application/json; charset=utf-8');
        echo $callback . '(' . json_encode($ret, JSON_UNESCAPED_UNICODE) . ')';
        exit;
    }

    /**
     * 原生响应
     *
     * @param        $data
     * @param string $type
     *
     * @author  liuchao
     */
    public static function raw($data, $type = 'json'){
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

}
