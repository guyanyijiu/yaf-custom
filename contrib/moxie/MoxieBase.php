<?php
namespace Moxie;
use Response;
/**
 * 魔蝎类文件
 */
class MoxieBase{

	const BASE_URL='https://api.51datakey.com/';	
	/**
	 * 发送请求
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function getRequest(array $data){
		$need['task_id']=isset($data['task_id'])?$data['task_id']:'';
		$param=isset($data['param'])?$data['param']:'';
		//判定参数
		if(empty($need['task_id'])||empty($param)){
			//参数错误
			Response::fail('参数错误');
		}
		//report需要email_id
		if($param=='mailCard_report'){
			$data['email_id']=isset($data['email_id'])?$data['email_id']:'';
		}
		switch ($param) {
			case 'alipay':
				$obj=new \Moxie\Alipay\FindInfo();
				break;
			case 'mailCard':
				$obj=new \Moxie\MailCard\FindInfo();
				break;
			case 'mailCard_report':
				$obj=new \Moxie\MailCard\Report();
				break;
			case 'onlineBank':
				$obj=new \Moxie\OnLineBank\FindInfo();
				break;
			
			default:
				Response::fail('请求错误');
				break;
		}
		$res=$obj->getData($need);
		$res=json_decode($res);
		if(isset($res->status) && $res->status!=200)
		{
			return json_encode($res);
		}
	    return json_encode($res);		
	}


}





?>