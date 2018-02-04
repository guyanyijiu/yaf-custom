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

        $request = (new \Request())->withYafRequest($request);
        $response = new \Response();

        $container->instance(\Request::class, $request);
        $container->instance(\Response::class, $response);

        $container->run($request, $response);

        // 结束
        exit;
    }

}
