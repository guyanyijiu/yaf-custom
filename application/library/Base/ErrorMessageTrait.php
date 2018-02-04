<?php

namespace Base;

/**
 * 错误信息管理
 *
 * Trait ErrorMessageTrait
 *
 * @package Base
 */
trait ErrorMessageTrait {
    /**
     * 错误信息
     *
     * @var mixed
     */
    private $error;

    /**
     * 设置错误信息
     *
     * @param $msg
     *
     * @author  liuchao
     */
    public function setError($msg) {
        $this->error = $msg;
    }

    /**
     * 获取错误信息
     *
     * @return null|string
     *
     * @author  liuchao
     */
    public function getError() {
        return $this->error;
    }
}