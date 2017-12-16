<?php

/**
 * Service 基类
 *
 * Class Service
 *
 * @author  liuchao
 */
class Service {

    /**
     * 错误信息
     *
     * @var mixed
     */
    protected $error;

    /**
     * 设置错误信息
     *
     * @param $msg
     *
     * @author  liuchao
     */
    public function setError($msg){
        $this->error = $msg;
    }

    /**
     * 获取错误信息
     *
     * @return null|string
     *
     * @author  liuchao
     */
    public function getError(){
        return $this->error;
    }

}