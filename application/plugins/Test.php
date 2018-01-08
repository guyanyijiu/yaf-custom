<?php

/**
 * 由于对框架执行流程进行了更改，所以 Plugin 仅支持两个方法 routerStartup 和 routerShutdown，未来可能增加新的方法
 *
 * Class TestPlugin
 *
 * @author  liuchao
 */
class TestPlugin extends Yaf_Plugin_Abstract {
    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    }
}
