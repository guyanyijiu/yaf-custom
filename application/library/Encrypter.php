<?php

class Encrypter {

    private $values;

    public function __construct($values = null) {
        if ( !is_null($values)) {
            $this->values = $values;
        }
    }

    // 设置值
    public function setValue($values = null) {
        $this->values = $values;

        return $this;
    }

    // 排序
    public function sort($order = 'asc', $by = 'key') {
        $this->checkValues('array');
        if ($order == 'asc') {
            $by == 'key' ? ksort($this->values) : asort($this->values);
        } else {
            $by == 'key' ? krsort($this->values) : arsort($this->values);
        }

        return $this;
    }

    // 连接
    public function concat($link = '=', $delimiter = '&') {
        $this->checkValues('array');
        $str = '';
        foreach ($this->values as $k => $v) {
            $str .= $k . $link . $v . $delimiter;
        }
        if ($str) {
            $len = strlen($delimiter);
            $str = substr($str, 0, -$len);
        }
        $this->values = $str;
        return $this;
    }

    // 过滤
    public function filter($except = [], $empty = true) {
        if (( !$except && !$empty) || !is_array($this->values)) {
            return $this;
        }
        foreach ($this->values as $k => $v) {
            if (in_array($k, $except) || ($empty && $v == '')) {
                unset($this->values[$k]);
            }
        }

        return $this;
    }

    // 签名
    public function sign($key = '', $algo = 'rsa', $mode = 'md5') {
        $this->checkValues('string');
        switch ($algo) {
            case 'rsa':
                $this->values = $this->rsaSign($key, $mode);
                break;
            default:
                $this->values = false;
        }

        return $this;
    }

    public function verifySign($key = '', $sign = '', $algo = 'rsa', $mode = 'md5') {
        $this->checkValues('string');
        switch ($algo) {
            case 'rsa':
                return $this->rsaVerifySign($key, $sign, $mode);
                break;
            default:
                return false;
        }

        return false;
    }

    // 编码
    public function encode($algo = 'base64') {
        $this->checkValues('string');
        switch ($algo) {
            case 'base64':
                $this->values = base64_encode($this->values);
                break;
            case 'url':
                $this->values = urlencode($this->values);
                break;
            case 'json':
                $this->values = json_encode($this->values);
                break;
            default:
                break;
        }

        return $this;
    }

    // 解码
    public function decode($algo = 'base64') {
        $this->checkValues('string');
        switch ($algo) {
            case 'base64':
                $this->values = base64_decode($this->values);
                break;
            case 'url':
                $this->values = urldecode($this->values);
                break;
            case 'json':
                $this->values = json_decode($this->values, true, 512);
                break;
            default:
                break;
        }

        return $this;
    }

    // RSA签名
    private function rsaSign($key, $mode) {
        return RSA::createSign($this->values, $key, $mode);
    }

    private function rsaVerifySign($key, $sign, $mode) {
        return RSA::verifySign($this->values, $sign, $key, $mode);
    }

    private function aes() {

    }

    private function des() {

    }

    private function md5() {

    }

    private function sha1() {

    }

    private function sha256() {

    }

    public function get() {
        return $this->values;
    }

    private function checkValues($expect = 'array') {
        switch ($expect) {
            case 'array':
                if ( !is_array($this->values)) {
                    throw new \Exception('待加密的参数不是数组类型');
                }
                break;
            case 'string':
                if ( !is_string($this->values)) {
                    throw new \Exception('待加密的参数不是字符串类型');
                }
                break;
        }

        return true;
    }
}