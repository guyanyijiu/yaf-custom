<?php

namespace Encryption;

/**
 * base64
 *
 * Class Base64
 *
 * @package Encryption
 * @author  liuchao
 */
class Base64 {

    /**
     * URL 安全编码
     *
     * @param $data
     *
     * @return string
     *
     * @author  liuchao
     */
    public static function urlSafeEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * URL 安全解码
     *
     * @param $data
     *
     * @return bool|string
     *
     * @author  liuchao
     */
    public static function urlSafeDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}