<?php

class RSA {

    public static $algo = [
        'md5'    => OPENSSL_ALGO_MD5,
        'sha1'   => OPENSSL_ALGO_SHA1,
        'sha256' => OPENSSL_ALGO_SHA256,
        'sha512' => OPENSSL_ALGO_SHA512,
    ];

    public static function encrypt($data, $publicKey) {

    }

    public static function decrypt($data, $privateKey) {

    }

    public static function createSign($data, $privateKey, $algo = 'md5') {
        // 加载密钥
        $key = openssl_get_privatekey($privateKey);

        if ($key === false) {
            return false;
        }
        // 生成签名
        $res = openssl_sign($data, $sign, $key, self::$algo[$algo]);
        // 释放密钥
        openssl_free_key($key);

        return $res ? $sign : false;
    }

    public static function verifySign($data, $sign, $publicKey, $algo = 'md5') {
        // 加载密钥
        $key = openssl_get_publickey($publicKey);
        if ($key === false) {
            return false;
        }
        // 校验签名
        $res = openssl_verify($data, $sign, $key, self::$algo[$algo]);
        // 释放密钥
        openssl_free_key($key);

        return $res === 1 ? true : false;
    }
}