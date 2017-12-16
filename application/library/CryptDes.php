<?php
/**
* Created by PhpStorm.
* User: rick
* Date: 15/9/8
* Time: 上午11:49
 *
 * PHP版DES加解密类
 * 可与java的DES(DESede/CBC/PKCS5Padding)加密方式兼容
*/

class CryptDes {
    var $key;
    var $iv;
    public function __construct($key, $iv){
        $this->key = $key;
        $this->iv = $iv;
    }

    function encrypt($input){
        $size = mcrypt_get_block_size(MCRYPT_DES,MCRYPT_MODE_CBC); //3DES加密将MCRYPT_DES改为MCRYPT_3DES
        $input = $this->pkcs5_pad($input, $size); //如果采用PaddingPKCS7，请更换成PaddingPKCS7方法。
        $key = str_pad($this->key,8,'0'); //3DES加密将8改为24
        $td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_CBC, '');
        if( $this->iv == '' )
        {
            $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        }
        else
        {
            $iv = $this->iv;
        }
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);//如需转换二进制可改成  bin2hex 转换
        return $data;
    }

    function decrypt($encrypted){
        $encrypted = base64_decode($encrypted); //如需转换二进制可改成  bin2hex 转换
        $key = str_pad($this->key,8,'0'); //3DES加密将8改为24
        $td = mcrypt_module_open(MCRYPT_DES,'',MCRYPT_MODE_CBC,'');//3DES加密将MCRYPT_DES改为MCRYPT_3DES
        if( $this->iv == '' )
        {
            $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        }
        else
        {
            $iv = $this->iv;
        }
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $encrypted);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $y=$this->pkcs5_unpad($decrypted);
        return $y;
    }

    protected function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    protected function pkcs5_unpad($text){
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad){
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }

    protected function PaddingPKCS7($data) {
        $block_size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);//3DES加密将MCRYPT_DES改为MCRYPT_3DES
        $padding_char = $block_size - (strlen($data) % $block_size);
        $data .= str_repeat(chr($padding_char),$padding_char);
        return $data;
    }


    /** * 用DES算法加密/解密字符串 * *
     *@param string $string 待加密的字符串
     *@param string $key 密匙，加解密需保持一致
     *@return string 返回经过加密/解密的字符串
     */
    static public function des_encrypt($string, $key="xxy") {
        $size = mcrypt_get_block_size('des', 'ecb');
        $string = mb_convert_encoding($string, 'GBK', 'UTF-8');
        $pad = $size - (strlen($string) % $size);
        $string = $string . str_repeat(chr($pad), $pad);
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    /** * 用DES算法加密/解密字符串 * *
     *@param string $string 待加密的字符串
     *@param string $key 密匙，加解密需保持一致
     *@return string 返回经过加密/解密的字符串
     */
    static public function des_decrypt($string, $key="xxy", $encode = true) {
        $string = base64_decode($string);
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        @$decrypted = mdecrypt_generic($td, $string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $pad = ord($decrypted{strlen($decrypted) - 1});
        if($pad > strlen($decrypted)) {
            return '';
        }
        if(strspn($decrypted, chr($pad), strlen($decrypted) - $pad) != $pad) {
            return '';
        }
        $result = substr($decrypted, 0, -1 * $pad);
        if($encode){
            $result = mb_convert_encoding($result, 'UTF-8', 'GBK');
        }

        return $result;
    }

    /*读取COOKIE信息	*/
    public static function get_session($key){
        $unser_list = @$_SESSION[$key];
        $list = CryptDes::des_decrypt($unser_list);//调用解密方法解密
        $result  =  unserialize($list);//unserialize 反序列化
        return $result;
    }

    /**
     * 解析身份证
     * @param $id_card
     * @return string
     */
    public static function deIDcard($string, $key = 'xxy'){
        $string = base64_decode($string);
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        @$decrypted = mdecrypt_generic($td, $string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $pad = ord($decrypted{strlen($decrypted) - 1});
        if($pad > strlen($decrypted)) {
            return '';
        }
        if(strspn($decrypted, chr($pad), strlen($decrypted) - $pad) != $pad) {
            return '';
        }
        $result = substr($decrypted, 0, -1 * $pad);
        $result = mb_convert_encoding($result, 'UTF-8', 'GBK');
        return $result;
    }

}

/*
$des = new CryptDes("1232","12345678");//（秘钥向量，混淆向量）
echo "\n";
echo $ret = $des->encrypt("1111");//加密字符串
echo "\n";
echo $des->decrypt($ret);
*/