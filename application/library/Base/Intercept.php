<?php

namespace Base;

/**
 * 在框架核心加载完成之后注册这个 Plugin，在 YAF 路由完成之后会调用 dispatchLoopStartup 方法，这个方法将接管后面的加载和响应流程
 *
 * Class DispatchPlugin
 *
 * @author  liuchao
 */
class Intercept extends \Yaf_Plugin_Abstract {

    public function dispatchLoopStartup(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response) {
        $container = container();

        $container->instance(\Request::class, (new \Request())->withYafRequest($request) );
        $container->instance(\Response::class, new \Response());

        $container->run();

        // 结束
        exit;
    }

}
