<?php
namespace Moxie\OnlineBank;
use Moxie\MoxieBase;
use Moxie\Https;

/**
 * 网银获取信息
 */
class FindInfo extends MoxieBase{

	const FINDINFO='bank/v3/allcards/';
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