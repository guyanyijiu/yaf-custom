<?php

namespace Base;

use ArrayObject;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * HttpResponse
 *
 * Class HttpResponse
 *
 * @author  liuchao
 */
class HttpResponse {

    /**
     * @var static
     */
    public static $instance;

    /**
     * @var array
     */
    public $headers;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $statusText;

    /**
     * @var array
     */
    public static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',                                     // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    ];

    /**
     * Constructor.
     *
     * @param mixed $content The response content, see setContent()
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     *
     * @throws \InvalidArgumentException When the HTTP status code is not valid
     */
    public function __construct($content = '', $status = 200, $headers = []) {
        $this->headers = $headers;
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion('1.1');

        static::$instance = $this;
    }

    /**
     * 发送 header
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function sendHeaders() {
        if (headers_sent()) {
            return $this;
        }

        if ( !isset($this->headers['Content-Type'])) {
            $this->headers['Content-Type'] = 'text/html; charset=utf-8';
        } elseif (0 === stripos($this->headers['Content-Type'], 'text/') && false === stripos($this->headers['Content-Type'], 'charset')) {
            $this->headers['Content-Type'] = $this->headers['Content-Type'] . '; charset=utf-8';
        }

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value, false, $this->statusCode);
        }

        header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText), true, $this->statusCode);

        return $this;
    }

    /**
     * 发送响应内容
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function sendContent() {
        echo $this->content;

        return $this;
    }

    /**
     * 发送响应
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function send() {
        $this->sendHeaders();
        $this->sendContent();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        return $this;
    }

    /**
     * 设置响应内容
     *
     * @param      $content
     * @param bool $append
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function setContent($content, $append = false) {

        if ($this->shouldBeJson($content)) {
            $this->setHeader('Content-Type', 'application/json; charset=utf-8');

            $content = $this->morphToJson($content);
        }

        if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable([$content, '__toString'])) {
            throw new \UnexpectedValueException(sprintf('The Response content must be a string or object implementing __toString(), "%s" given.', gettype($content)));
        }

        $this->content = $append == true ? $this->content . (string) $content : (string) $content;

        return $this;
    }

    /**
     * 设置一个 header
     *
     * @param      $key
     * @param      $value
     * @param bool $replace
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function setHeader($key, $value, $replace = true) {
        if ( !isset($this->headers[$key]) || $replace == true) {
            $this->headers[$key] = $value;
        }

        return $this;
    }

    /**
     * 设置多个 header
     *
     * @param array $headers
     * @param bool  $replace
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function setHeaders(array $headers, $replace = true) {
        foreach ($headers as $k => $v) {
            if ( !isset($this->headers[$k]) || $replace == true) {
                $this->headers[$k] = $v;
            }
        }

        return $this;
    }

    /**
     * 检测是否可以编码为 json
     *
     * @param $content
     *
     * @return bool
     *
     * @author  liuchao
     */
    protected function shouldBeJson($content) {
        return $content instanceof Arrayable ||
            $content instanceof Jsonable ||
            $content instanceof ArrayObject ||
            $content instanceof JsonSerializable ||
            is_array($content);
    }

    /**
     * 将 content 转为 json
     *
     * @param $content
     *
     * @return string
     *
     * @author  liuchao
     */
    protected function morphToJson($content) {
        if ($content instanceof Jsonable) {
            return $content->toJson();
        } elseif ($content instanceof Arrayable) {
            return json_encode($content->toArray(), JSON_UNESCAPED_UNICODE);
        }

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }


    /**
     * 获取 content
     *
     * @return string
     *
     * @author  liuchao
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * 设置 http 协议版本号
     *
     * @param $version
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function setProtocolVersion($version) {
        $this->version = $version;

        return $this;
    }

    /**
     * 获取 http 协议版本号
     *
     * @return string
     *
     * @author  liuchao
     */
    public function getProtocolVersion() {
        return $this->version;
    }

    /**
     * 设置 http 状态码
     *
     * @param      $code
     * @param null $text
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function setStatusCode($code, $text = null) {
        $this->statusCode = $code = (int) $code;
        if ($this->isInvalid()) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
        }

        if (null === $text) {
            $this->statusText = isset(self::$statusTexts[$code]) ? self::$statusTexts[$code] : 'unknown status';

            return $this;
        }

        if (false === $text) {
            $this->statusText = '';

            return $this;
        }

        $this->statusText = $text;

        return $this;
    }

    /**
     * 获取 http 状态码
     *
     * @return int
     *
     * @author  liuchao
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * 是否是有效的状态码
     *
     * @return bool
     *
     * @author  liuchao
     */
    public function isInvalid() {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

}
