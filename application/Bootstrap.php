<?php

/**
 * 框架引导文件
 *
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 *
 * @Author   liuchao
 * Class Bootstrap
 */

class Bootstrap extends Yaf_Bootstrap_Abstract {

    /**
     * 引入基础文件
     *
     * @Author   liuchao
     */
    public function _initLoadFile() {
        // 引入composer自动加载
        Yaf_Loader::import(ROOT_PATH . '/vendor/autoload.php');
        // 引入辅助函数
        Yaf_Loader::import(APP_PATH . '/helpers.php');
    }

    /**
     * 初始化
     *
     * @Author   liuchao
     *
     * @param \Yaf_Dispatcher $dispatcher
     */
    public function _initConfig(Yaf_Dispatcher $dispatcher) {
        // 注册异常处理
        \HandleExceptions::register();

        // 关闭YAF自动渲染
        $dispatcher->autoRender(false);

        // 设置requestId
        $requestId = $dispatcher->getRequest()->getServer('HTTP_QX_REQUESTID');
        if ($requestId) {
            \Uniqid::setRequestId($requestId);
        }

        // 注册路由执行插件
        $dispatcher->registerPlugin(new \Base\Intercept());

        // 实例化一个容器对象
        $container = new \Base\Container();

        Yaf_Registry::set('container', $container);
        
        // 注册config
        $container->singleton('config', function () {
            return new \Config(CONF_PATH);
        });

        require APP_PATH . '/Application.php';
    }

}
