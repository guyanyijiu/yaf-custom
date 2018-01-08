<?php

/**
 * 在框架核心加载完成之后注册这个 Plugin，在 YAF 路由完成之后会调用 dispatchLoopStartup 方法，这个方法将接管后面的加载和响应流程
 *
 * Class DispatchPlugin
 *
 * @author  liuchao
 */
class DispatchPlugin extends Yaf_Plugin_Abstract {

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        // 构建 Request
        $request = (new \Base\HttpRequest())->withYafRequest($request);
        $request = new \Request($request);
        // 注册到容器
        container()->instance('Request', $request);
        // 执行路由
        (new \Base\HttpDispatch())->run($request);
        // 结束
        exit;
    }

}
