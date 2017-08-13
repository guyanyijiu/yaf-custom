<?php

class TestPlugin extends Yaf_Plugin_Abstract{
    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
       // echo 'routerStartup<br>';
    }
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
//        echo 'routerShutdown<br>';
    }
    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
//        echo 'dispatchLoopStartup<br>';
    }
    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
//        echo 'preDispatch<br>';
    }
    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
//        echo 'postDispatch<br>';
    }
    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
//        echo 'dispatchLoopShutdown<br>';
    }
}
