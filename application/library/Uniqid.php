<?php

/**
 * 获取唯一ID
 *
 * Class Uniqid
 *
 * @author  liuchao
 */
class Uniqid {

    /**
     * 唯一请求ID
     *
     * @var string
     */
    private static $requestId;

    /**
     * 获取唯一请求ID
     *
     * @return string
     *
     * @author  liuchao
     */
    public static function getRequestId() {
        if (is_null(static::$requestId)) {
            static::$requestId = str_replace('.', '', gethostname() . '-' . uniqid(mt_rand(10000, 99999), true));
        }

        return static::$requestId;
    }

    /**
     * 设置唯一请求ID
     *
     * @param $requestId
     *
     * @author  liuchao
     */
    public static function setRequestId($requestId) {
        static::$requestId = $requestId;
    }

    /**
     * 获取随机ID
     *
     * @return string
     *
     * @author  liuchao
     */
    public static function random() {
        return uniqid();
    }

}