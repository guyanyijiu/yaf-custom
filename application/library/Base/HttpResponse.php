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
     * @var array
     */
    public $headers;

    /**
     * @var mixed
     */
    public $original;

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
     * Status codes translation table.
     *
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
        // 跨域设置
        if (config('application.app_cors')) {
            $headers['Access-Control-Allow-Origin'] = config('application.app_cors_origin');
            $headers['Access-Control-Allow-Methods'] = config('application.app_cors_methods');
            $headers['Access-Control-Allow-Headers'] = config('application.app_cors_headers');
        }

        $this->headers = $headers;
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion('1.1');


    }

    /**
     * Sends HTTP headers.
     *
     * @return $this
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
     * Sends content for the current web response.
     *
     * @return $this
     */
    public function sendContent() {
        echo $this->content;

        return $this;
    }

    /**
     * Sends HTTP headers and content.
     *
     * @return $this
     */
    public function send() {
        $this->sendHeaders();
        $this->sendContent();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        return $this;
    }

    public function setContent($content) {
        $this->original = $content;

        if ($this->shouldBeJson($content)) {
            $this->setHeader('Content-Type', 'application/json; charset=utf-8');

            $content = $this->morphToJson($content);
        }

        if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable([$content, '__toString'])) {
            throw new \UnexpectedValueException(sprintf('The Response content must be a string or object implementing __toString(), "%s" given.', gettype($content)));
        }

        $this->content = (string) $content;

        return $this;
    }

    public function setHeader($key, $value, $replace = true) {
        if ( !isset($this->headers[$key]) || $replace == true) {
            $this->headers[$key] = $value;
        }

        return $this;
    }

    /**
     * Determine if the given content should be turned into JSON.
     *
     * @param  mixed $content
     *
     * @return bool
     */
    protected function shouldBeJson($content) {
        return $content instanceof Arrayable ||
            $content instanceof Jsonable ||
            $content instanceof ArrayObject ||
            $content instanceof JsonSerializable ||
            is_array($content);
    }

    /**
     * Morph the given content into JSON.
     *
     * @param  mixed $content
     *
     * @return string
     */
    protected function morphToJson($content) {
        if ($content instanceof Jsonable) {
            return $content->toJson();
        } elseif ($content instanceof Arrayable) {
            return json_encode($content->toArray());
        }

        return json_encode($content);
    }


    /**
     * Gets the current response content.
     *
     * @return string Content
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Sets the HTTP protocol version (1.0 or 1.1).
     *
     * @param string $version The HTTP protocol version
     *
     * @return $this
     *
     * @final since version 3.2
     */
    public function setProtocolVersion($version) {
        $this->version = $version;

        return $this;
    }

    /**
     * Gets the HTTP protocol version.
     *
     * @return string The HTTP protocol version
     *
     * @final since version 3.2
     */
    public function getProtocolVersion() {
        return $this->version;
    }

    /**
     * Sets the response status code.
     *
     * @param int   $code HTTP status code
     * @param mixed $text HTTP status text
     *
     * If the status text is null it will be automatically populated for the known
     * status codes and left empty otherwise.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException When the HTTP status code is not valid
     *
     * @final since version 3.2
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
     * Retrieves the status code for the current web response.
     *
     * @return int Status code
     *
     * @final since version 3.2
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
