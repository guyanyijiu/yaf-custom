<?php

namespace Encryption;

/**
 * RSA 加解密类
 *
 * Class RSA
 *
 * @package Encryption
 * @author  liuchao
 */
class RSA {

    /**
     * 支持的签名算法
     *
     * @var array
     */
    public static $algo = [
        'md5'    => OPENSSL_ALGO_MD5,
        'sha1'   => OPENSSL_ALGO_SHA1,
        'sha256' => OPENSSL_ALGO_SHA256,
        'sha512' => OPENSSL_ALGO_SHA512,
    ];

    /**
     * 公钥加密
     *
     * @param     $data
     * @param     $publicKey
     * @param int $keyLen
     * @param int $padding
     *
     * @return bool|string
     *
     * @author  liuchao
     */
    public static function publicEncrypt($data, $publicKey, $keyLen = 1024, $padding = OPENSSL_PKCS1_PADDING) {
        $key = openssl_get_publickey($publicKey);
        if ($key === false) {
            return false;
        }

        $encrypted = '';
        $part_len = $keyLen / 8 - 11;
        $parts = str_split($data, $part_len);
        foreach ($parts as $part) {
            $encrypted_temp = '';
            openssl_public_encrypt($part, $encrypted_temp, $key, $padding);
            $encrypted .= $encrypted_temp;
        }

        return $encrypted;
    }

    /**
     * 私钥加密
     *
     * @param     $data
     * @param     $privateKey
     * @param int $keyLen
     * @param int $padding
     *
     * @return bool|string
     *
     * @author  liuchao
     */
    public static function privateEncrypt($data, $privateKey, $keyLen = 1024, $padding = OPENSSL_PKCS1_PADDING) {
        $key = openssl_get_privatekey($privateKey);
        if ($key === false) {
            return false;
        }

        $encrypted = '';
        $part_len = $keyLen / 8 - 11;
        $parts = str_split($data, $part_len);
        foreach ($parts as $part) {
            $encrypted_temp = '';
            openssl_private_encrypt($part, $encrypted_temp, $key, $padding);
            $encrypted .= $encrypted_temp;
        }

        return $encrypted;
    }

    /**
     * 公钥解密
     *
     * @param     $data
     * @param     $publicKey
     * @param int $keyLen
     * @param int $padding
     *
     * @return bool|string
     *
     * @author  liuchao
     */
    public static function publicDecrypt($data, $publicKey, $keyLen = 1024, $padding = OPENSSL_PKCS1_PADDING) {
        $key = openssl_get_publickey($publicKey);
        if ($key === false) {
            return false;
        }

        $decrypted = "";
        $part_len = $keyLen / 8;
        $parts = str_split($data, $part_len);

        foreach ($parts as $part) {
            $decrypted_temp = '';
            openssl_public_decrypt($part, $decrypted_temp, $key, $padding);
            $decrypted .= $decrypted_temp;
        }

        return $decrypted;
    }

    /**
     * 私钥解密
     *
     * @param     $data
     * @param     $privateKey
     * @param int $keyLen
     * @param int $padding
     *
     * @return bool|string
     *
     * @author  liuchao
     */
    public static function privateDecrypt($data, $privateKey, $keyLen = 1024, $padding = OPENSSL_PKCS1_PADDING) {
        $key = openssl_get_privatekey($privateKey);
        if ($key === false) {
            return false;
        }

        $decrypted = "";
        $part_len = $keyLen / 8;
        $parts = str_split($data, $part_len);

        foreach ($parts as $part) {
            $decrypted_temp = '';
            openssl_private_decrypt($part, $decrypted_temp, $key, $padding);
            $decrypted .= $decrypted_temp;
        }

        return $decrypted;
    }

    /**
     * 签名
     *
     * @param        $data
     * @param        $privateKey
     * @param string $algo
     *
     * @return bool
     *
     * @author  liuchao
     */
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

    /**
     * 验签
     *
     * @param        $data
     * @param        $sign
     * @param        $publicKey
     * @param string $algo
     *
     * @return bool
     *
     * @author  liuchao
     */
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