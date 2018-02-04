<?php

namespace Base;

/**
 * Class HttpRequest
 *
 * @package Base
 * @author  liuchao
 */
class HttpRequest {

    /**
     * @var static
     */
    public static $instance;

    /**
     * 模块名
     *
     * @var string
     */
    public $module;

    /**
     * 控制器名
     *
     * @var string
     */
    public $controller;

    /**
     * 方法名
     *
     * @var string
     */
    public $action;

    /**
     * 请求方法
     *
     * @var string
     */
    public $method;

    /**
     * 请求 URI
     *
     * @var string
     */
    protected $uri;

    /**
     * GET 参数
     *
     * @var array
     */
    protected $get;

    /**
     * POST 参数
     *
     * @var array
     */
    protected $post;

    /**
     * SERVER
     *
     * @var array
     */
    protected $server;

    /**
     * 原生请求 body
     *
     * @var string
     */
    protected $content;


    /**
     * HttpRequest constructor.
     *
     * @param array $get
     * @param array $post
     * @param array $server
     * @param null  $content
     */
    public function __construct($get = [], $post = [], $server = [], $content = null) {
        $this->setGet($get);
        $this->setPost($post);
        $this->setServer($server);
        $this->setContent($content);

        static::$instance = $this;
    }

    /**
     * 使用 Yaf_Request 对象构建
     *
     * @param \Yaf_Request_Abstract $request
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function withYafRequest(\Yaf_Request_Abstract $request) {

        $this->setModule($request->getModuleName());
        $this->setController($request->getControllerName());
        $this->setAction($request->getActionName());

        $this->setUri($request->getRequestUri());
        $this->setServer($_SERVER);
        $this->setGet($request->getQuery());
        $this->setPost($request->getPost());
        $this->setContent(file_get_contents('php://input'));
        $this->setMethod($request->getMethod());

        return $this;
    }

    /**
     * 创建一个新的请求实例
     *
     * @param       $uri
     * @param       $method
     * @param array $get
     * @param array $post
     * @param array $server
     * @param null  $content
     *
     * @return static
     * @throws \Exception
     *
     * @author  liuchao
     */
    public static function create($uri, $method, $get = [], $post = [], $server = [], $content = null) {
        if ( !preg_match('/^\/([a-zA-Z_]+)\/([a-zA-Z_]+)\/([a-zA-Z_]+)$/', $uri, $matches)) {
            throw new \Exception('Bad URL :' . $uri);
        }

        if ( !in_array(strtoupper($method), [
            'GET',
            'POST',
        ])) {
            throw new \Exception('Bad Method :' . $method);
        }

        $instance = new static($get, $post, $server, $content);

        $instance->setUri($uri);
        $instance->setMethod($method);

        $instance->setModule($matches[1]);
        $instance->setController($matches[2]);
        $instance->setAction($matches[3]);

        $instance->setServer($server);
        $instance->setContent($content);

        return $instance;
    }

    /**
     * 设置 module
     *
     * @param $module
     *
     * @author  liuchao
     */
    public function setModule($module) {
        $this->module = $module;
    }

    /**
     * 设置 controller
     *
     * @param $controller
     *
     * @author  liuchao
     */
    public function setController($controller) {
        $this->controller = $controller;
    }

    /**
     * 设置 action
     *
     * @param $action
     *
     * @author  liuchao
     */
    public function setAction($action) {
        $this->action = $action;
    }

    /**
     * 设置请求方法
     *
     * @param $method
     *
     * @author  liuchao
     */
    public function setMethod($method) {
        $this->method = $method;
    }

    /**
     * 设置 URI
     *
     * @param $uri
     *
     * @author  liuchao
     */
    public function setUri($uri) {
        $this->uri = $uri;
    }

    /**
     * 设置 GET 值
     *
     * @param $get
     *
     * @author  liuchao
     */
    public function setGet($get) {
        $this->get = $get;
    }

    /**
     * 设置 POST 值
     *
     * @param $post
     *
     * @author  liuchao
     */
    public function setPost($post) {
        $this->post = $post;
    }

    /**
     * 设置 SERVER
     *
     * @param $server
     *
     * @author  liuchao
     */
    public function setServer($server) {
        $this->server = $server;
    }

    /**
     * 设置请求 body
     *
     * @param $content
     *
     * @author  liuchao
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * 获取 module
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * 获取 controller
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * 获取 action
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * 获取请求方法
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * 获取 URI
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * 获取 GET 值
     *
     * @param null $name
     * @param null $default
     *
     * @return null
     *
     * @author  liuchao
     */
    public function getGet($name = null, $default = null) {
        if (is_null($name)) {
            return $this->get;
        }

        return isset($this->get[$name]) ? $this->get[$name] : $default;
    }

    /**
     * 获取 POST 值
     *
     * @param null $name
     * @param null $default
     *
     * @return null
     *
     * @author  liuchao
     */
    public function getPost($name = null, $default = null) {
        if (is_null($name)) {
            return $this->post;
        }

        return isset($this->post[$name]) ? $this->post[$name] : $default;
    }

    /**
     * 获取 SERVER 值
     *
     * @param null $name
     *
     * @return null
     *
     * @author  liuchao
     */
    public function getServer($name = null) {
        if (is_null($name)) {
            return $this->server;
        }

        return isset($this->server[$name]) ? $this->server[$name] : null;
    }

    /**
     * 获取原生请求体
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * 获取header
     *
     * @param      $key
     * @param null $default
     *
     * @return array|mixed|null
     *
     * @author  liuchao
     */
    public function getHeader($key, $default = null) {
        $headers = [];
        foreach ($this->server as $name => $value) {
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


}
