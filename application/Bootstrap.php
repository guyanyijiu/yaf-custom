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

        // 注册config
        $container->singleton('config', function () {
            return new \Config(CONF_PATH);
        });

        // 注册db
        $container->singleton('db', function ($container) {
            return new \DB($container);
        });

        // 注册 redis
        $container->singleton('redis', function ($container) {
            $config = $container->make('config')->get('database.redis');
            $driver = $config['client'];
            unset($config['client']);

            return new \Illuminate\Redis\RedisManager($driver, $config);
        });

        Yaf_Registry::set('container', $container);
    }

    /**
     * 注册自定义服务
     *
     * @Author   liuchao
     */
//    public function _initContainer() {
//        $container = container();
//    }

    /**
     * 注册中间件
     *
     * 按照中间件注册的顺序，请求处理前逻辑是倒序执行，请求处理后逻辑是正序执行
     *
     * @author  liuchao
     */
//    public function _initMiddleware(){
//        $container = container();

//        $container->middleware(function(\Request $request, \Response $response, $next){
//            // 请求处理前逻辑
//
//            $response = $next($request, $response);
//
//            // 请求处理后逻辑
//
//            return $response;
//        });

//        $container->middleware([
//            Middleware\Example::class,
//        ]);
//    }

}
