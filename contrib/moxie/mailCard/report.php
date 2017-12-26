<?php
namespace Moxie\MailCard;
use Moxie\MoxieBase;
use Moxie\Https;

/**
 * 邮箱信用卡报告
 */
class Report extends MoxieBase{

	const REPORT='email/v2/report';
	/**
	 * 获取数据
	 * @param  [type] $task_id [description]
	 * @return [type]          [description]
	 */
	public function getData($data){
		$url=self::BASE_URL.self::REPORT.'/'.$data['task_id'].'/'.$data['email_id'];
		$res=Https::get($url);
		return $res;
	}


}





?>