<?php

/**
 * 简化请求类的使用
 *
 * @Author   liuchao
 *
 * Class Request
 */

class Request extends \Base\HttpRequest {

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
        return $this->getGet($name, $default);
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
        return $this->getPost($name, $default);
    }

    /**
     * 获取原生 body
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function raw() {
        return $this->getContent();
    }

    /**
     * 获取 header
     *
     * @param null $name
     * @param null $default
     *
     * @return array|mixed|null
     *
     * @author  liuchao
     */
    public function header($name = null, $default = null) {
        return $this->getHeader($name, $default);
    }

}
