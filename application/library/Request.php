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
     * HttpRequest
     *
     * @var \Base\HttpRequest
     */
    private $request;

    /**
     * Request constructor.
     *
     * @param \Base\HttpRequest $request
     */
    public function __construct(\Base\HttpRequest $request) {
        $this->request = $request;
    }

    /**
     * 获取 GET 参数
     *
     * @param null $name
     * @param null $default
     *
     * @return null
     *
     * @author  liuchao
     */
    public function get($name = null, $default = null) {
        return $this->request->getGet($name, $default);
    }

    /**
     * 获取 POST 参数
     *
     * @param null $name
     * @param null $default
     *
     * @return null
     *
     * @author  liuchao
     */
    public function post($name = null, $default = null) {
        return $this->request->getPost($name, $default);
    }

    /**
     * 获取原生 body
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function raw() {
        return $this->request->getContent();
    }

    /**
     * 获取 header
     *
     * @param null $name
     * @param null $default
     *
     * @author  liuchao
     */
    public function header($name = null, $default = null) {
        $this->request->getHeader($name, $default);
    }

    /**
     * __get
     *
     * @param $name
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function __get($name) {
        return $this->request->$name;
    }

}
