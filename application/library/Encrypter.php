<?php

/**
 * 加解密类
 *
 * Class Encrypter
 *
 * @author  liuchao
 */
class Encrypter {

    /**
     * 待处理的值
     *
     * @var mixed
     */
    private $data;

    /**
     * Encrypter constructor.
     *
     * @param null $data
     */
    public function __construct($data = null) {
        if ( !is_null($data)) {
            $this->data = $data;
        }
    }

    /**
     * 设置值
     *
     * @param null $data
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function setData($data = null) {
        $this->data = $data;

        return $this;
    }

    /**
     * 获取结果
     *
     * @return null
     *
     * @author  liuchao
     */
    public function getResult() {
        return $this->data;
    }

    /**
     * 排序
     *
     * @param string $order
     * @param string $by
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function sort($order = 'asc', $by = 'key') {
        if ($order == 'asc') {
            $by == 'key' ? ksort($this->data) : asort($this->data);
        } else {
            $by == 'key' ? krsort($this->data) : arsort($this->data);
        }

        return $this;
    }

    /**
     * 拼接
     *
     * @param string $link
     * @param string $delimiter
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function concat($link = '=', $delimiter = '&') {
        $str = '';
        foreach ($this->data as $k => $v) {
            $str .= $k . $link . $v . $delimiter;
        }
        if ($str) {
            $len = strlen($delimiter);
            $str = substr($str, 0, -$len);
        }
        $this->data = $str;

        return $this;
    }

    /**
     * 过滤
     *
     * @param array $except
     * @param bool  $empty
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function filter($except = [], $empty = true) {
        if (( !$except && !$empty) || !is_array($this->data)) {
            return $this;
        }
        foreach ($this->data as $k => $v) {
            if (in_array($k, $except) || ($empty && $v == '')) {
                unset($this->data[$k]);
            }
        }

        return $this;
    }

    /**
     * 默认方法加密
     *
     * @param        $key
     * @param string $cipher
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function encrypt($key, $cipher = 'AES-256-CBC') {
        $this->data = (new \Encryption\Encrypter($key, $cipher))->encrypt($this->data);

        return $this;
    }

    /**
     * 默认方法解密
     *
     * @param        $key
     * @param string $cipher
     *
     * @return $this
     * @throws Exception
     *
     * @author  liuchao
     */
    public function decrypt($key, $cipher = 'AES-256-CBC') {
        $this->data = (new \Encryption\Encrypter($key, $cipher))->decrypt($this->data);

        return $this;
    }

    /**
     * AES 加密
     *
     * @param        $key
     * @param string $iv
     * @param string $cipher
     * @param int    $option
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function aesEncrypt($key, $iv = '', $cipher = 'AES-256-ECB', $option = 0) {
        $this->data = openssl_encrypt($this->data, $cipher, $key, $option, $iv);

        return $this;
    }

    /**
     * AES 解密
     *
     * @param        $key
     * @param string $iv
     * @param string $cipher
     * @param int    $option
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function aesDecrypt($key, $iv = '', $cipher = 'AES-256-ECB', $option = 0) {
        $this->data = openssl_decrypt($this->data, $cipher, $key, $option, $iv);

        return $this;
    }

    /**
     * DES 加密
     *
     * @param        $key
     * @param string $iv
     * @param string $cipher
     * @param int    $option
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function desEncrypt($key, $iv = '', $cipher = 'DES-ECB', $option = 0) {
        $this->data = openssl_encrypt($this->data, $cipher, $key, $option, $iv);

        return $this;
    }

    /**
     * DES 解密
     *
     * @param        $key
     * @param string $iv
     * @param string $cipher
     * @param int    $option
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function desDecrypt($key, $iv = '', $cipher = 'DES-ECB', $option = 0) {
        $this->data = openssl_decrypt($this->data, $cipher, $key, $option, $iv);

        return $this;
    }

    /**
     * RSA 加密
     *
     * @param     $publicKey
     * @param int $padding
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function rsaEncrypt($publicKey, $padding = OPENSSL_PKCS1_PADDING) {
        $this->data = \Encryption\RSA::encrypt($this->data, $publicKey, $padding);

        return $this;
    }

    /**
     * RSA 解密
     *
     * @param     $privateKey
     * @param int $padding
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function rsaDecrypt($privateKey, $padding = OPENSSL_PKCS1_PADDING) {
        $this->data = \Encryption\RSA::decrypt($this->data, $privateKey, $padding);

        return $this;
    }

    /**
     * RSA 签名
     *
     * @param        $privateKey
     * @param string $algo
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function rsaSign($privateKey, $algo = 'md5') {
        $this->data = \Encryption\RSA::createSign($this->data, $privateKey, $algo);

        return $this;
    }

    /**
     * RSA 验证签名
     *
     * @param        $sign
     * @param        $publicKey
     * @param string $algo
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function rsaVerifySign($sign, $publicKey, $algo = 'md5') {
        $this->data = \Encryption\RSA::verifySign($this->data, $sign, $publicKey, $algo);

        return $this;
    }

    /**
     * base64 编码
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function base64Encode() {
        $this->data = base64_encode($this->data);

        return $this;
    }

    /**
     * base64 解码
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function base64Decode() {
        $this->data = base64_decode($this->data);

        return $this;
    }

    /**
     * URL 安全的 base64 编码
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function urlSafeBase64Encode() {
        $this->data = rtrim(strtr(base64_encode($this->data), '+/', '-_'), '=');

        return $this;
    }

    /**
     * URL 安全的 base64 解码
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function urlSafeBase64Decode() {
        $this->data = base64_decode(str_pad(strtr($this->data, '-_', '+/'), strlen($this->data) % 4, '=', STR_PAD_RIGHT));

        return $this;
    }

    /**
     * md5 编码
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function md5() {
        $this->data = md5($this->data);

        return $this;
    }

    /**
     * md5 解码
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function sha256() {
        $this->data = hash('sha256', $this->data);

        return $this;
    }

}