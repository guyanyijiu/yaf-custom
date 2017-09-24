<?php

/**
 * 处理请求参数
 *
 * @Author   liuchao
 *
 * Class RequestPlugin
 */
class RequestPlugin extends Yaf_Plugin_Abstract{

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){

    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
        //处理参数加解密和验证等公共逻辑
    }

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){

    }

    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){

    }

    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){

    }

    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){

    }

}
