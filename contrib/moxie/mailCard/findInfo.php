<?php
namespace Moxie\MailCard;
use Moxie\MoxieBase;
use Moxie\Https;

/**
 * 邮箱信用卡获取信息
 */
class FindInfo extends MoxieBase{

	const FINDINFO='email/v2/alldata/';
	/**
	 * 获取数据
	 * @param  [type] $task_id [description]
	 * @return [type]          [description]
	 */
	public function getData($data){
		$url=self::BASE_URL.self::FINDINFO;
		$res=Https::get($url,$data['task_id']);
		return $res;
	}


}





?>