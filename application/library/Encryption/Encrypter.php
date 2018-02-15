<?php

namespace Encryption;

/**
 *  AES HMAC 加解密算法，作为默认的加解密算法
 *
 * Class Encrypter
 *
 * @package Encryption
 * @author  liuchao
 */
class Encrypter {

    /**
     * key
     *
     * @var string
     */
    protected $key;

    /**
     * AES 算法
     *
     * @var string
     */
    protected $cipher;

    /**
     * AES 偏移量
     *
     * @var string
     */
    protected $iv;

    /**
     * Encrypter constructor.
     *
     * @param        $key
     * @param string $cipher
     */
    public function __construct($key, $cipher = 'AES-128-CBC') {

        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));

        $key = (string) $key;

        if (static::supported($key, $cipher)) {
            $this->key = $key;
            $this->cipher = $cipher;
        } else {
            throw new \RuntimeException('The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.');
        }
    }

    /**
     * key 是否支持
     *
     * @param $key
     * @param $cipher
     *
     * @return bool
     *
     * @author  liuchao
     */
    public static function supported($key, $cipher) {
        $length = mb_strlen($key, '8bit');

        return ($cipher === 'AES-128-CBC' && $length === 16) ||
            ($cipher === 'AES-256-CBC' && $length === 32);
    }

    /**
     * 加密
     *
     * @param $value
     *
     * @return string
     *
     * @author  liuchao
     */
    public function encrypt($value) {

        $value = \openssl_encrypt($value, $this->cipher, $this->key, 0, $this->iv);

        if ($value === false) {
            throw new \RuntimeException('Could not encrypt the data.');
        }

        $mac = $this->hash($iv = base64_encode($this->iv), $value);
        $value = $iv . '.' . $value . '.' . $mac;

        return base64_encode($value);
    }

    /**
     * 解密
     *
     * @param $payload
     *
     * @return string
     * @throws \Exception
     *
     * @author  liuchao
     */
    public function decrypt($payload) {
        $payload = $this->parsePayload($payload);

        $iv = base64_decode($payload['iv']);

        $decrypted = \openssl_decrypt($payload['value'], $this->cipher, $this->key, 0, $iv);

        if ($decrypted === false) {
            throw new \RuntimeException('Could not decrypt the data.');
        }

        return $decrypted;
    }

    /**
     * hash 签名
     *
     * @param $iv
     * @param $value
     *
     * @return string
     *
     * @author  liuchao
     */
    protected function hash($iv, $value) {
        return hash_hmac('sha256', $iv . $value, $this->key);
    }

    /**
     * 解析密文
     *
     * @param $payload
     *
     * @return array
     * @throws \Exception
     *
     * @author  liuchao
     */
    protected function parsePayload($payload) {
        $payload = explode('.', base64_decode($payload));
        if (count($payload) != 3) {
            throw new \RuntimeException('The payload is invalid.');
        }
        $payload = [
            'iv'    => $payload[0],
            'value' => $payload[1],
            'mac'   => $payload[2],
        ];

        if ( !$this->validMac($payload)) {
            throw new \RuntimeException('The MAC is invalid.');
        }

        return $payload;
    }

    /**
     * 校验签名
     *
     * @param array $payload
     *
     * @return bool
     * @throws \Exception
     *
     * @author  liuchao
     */
    protected function validMac(array $payload) {
        $calculated = $this->calculateMac($payload, $bytes = random_bytes(16));

        return hash_equals(
            hash_hmac('sha256', $payload['mac'], $bytes, true), $calculated
        );
    }

    /**
     * 计算签名
     *
     * @param $payload
     * @param $bytes
     *
     * @return string
     *
     * @author  liuchao
     */
    protected function calculateMac($payload, $bytes) {
        return hash_hmac(
            'sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true
        );
    }

}
