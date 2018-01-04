<?php
namespace Moxie;
/**
 * Https请求
 */
class Https{
	static $result;
	/**
	 * 处理字符串
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public static function getParam($data){
		$param='?';
		if(is_array($data)){
			foreach ($data as $key => $value) {
				$param.=$key.'='.$value.'&';
			}
		}
		$param=substr($param,0,-1);
		return $param;
	}

	public static  function get($url,$data=null){
		$param=self::getParam($data);
		$ch = curl_init();
		$headers=array('Authorization:'.'token 12de24e4337b4ce0adda34fd3b2f22a2');			
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch, CURLOPT_URL,$url.$param); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		self::$result = curl_exec($ch);
		curl_close($ch);
		return self::$result;
	}
   




}
?>