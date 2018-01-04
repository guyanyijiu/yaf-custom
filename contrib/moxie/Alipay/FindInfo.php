<?php
namespace Moxie\Alipay;
use Moxie\MoxieBase;
use Moxie\Https;
use Http;
/**
 * 支付宝获取信息
 */
class FindInfo extends MoxieBase{

	const FINDINFO='gateway/alipay/v5/data';
	/**
	 * 获取数据
	 * @param  [type] $task_id [description]
	 * @return [type]          [description]
	 */
	public function getData($data){
		$url=self::BASE_URL.self::FINDINFO.'/'.$data['task_id'];
		$res=Https::get($url);
		return $res;
	}


}

?>