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
     * 初始化设置
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
    }

    /**
     * 引入composer自动加载
     *
     * @Author   liuchao
     */
    public function _initComposer() {
        Yaf_Loader::import(ROOT_PATH . '/vendor/autoload.php');
    }

    /**
     * 引入辅助函数
     *
     * @Author   liuchao
     */
    public function _initHelpers() {
        Yaf_Loader::import(ROOT_PATH . '/helper/functions.php');
        Yaf_Loader::import(ROOT_PATH . '/helper/helpers.php');
    }

    /**
     * 加载容器，注册类库
     *
     * @Author   liuchao
     */
    public function _initContainer() {

        // 实例化一个容器对象
        $container = new \Illuminate\Container\Container();

        // 注册config
        $container->singleton('config', function () {
            return new \Config(CONF_PATH);
        });

        // 注册db
        $container->singleton('db', function ($container) {
            return new \DB($container);
        });

        // 注册 redis
        $container->singleton('Redis', function ($container) {
            $config = $container->make('config')->get('database.redis');
            $driver = $config['client'];
            unset($config['client']);
            return new \Illuminate\Redis\RedisManager($driver, $config);
        });

        Yaf_Registry::set('container', $container);
    }

    /**
     *  生成唯一请求ID
     *
     * @Author   liuchao
     *
     * @param \Yaf_Dispatcher $dispatcher
     */
    public function _initRequestId(Yaf_Dispatcher $dispatcher) {
        // 先获取传递的requestId
        $requestId = $dispatcher->getRequest()->getServer('HTTP_QX_REQUESTID');
        if ($requestId) {
            \Uniqid::setRequestId($requestId);
        }

    }

    /**
     * 加载配置文件中自定义路由
     *
     * @Author   liuchao
     *
     * @param \Yaf_Dispatcher $dispacher
     */
    //     public function _initRoute(Yaf_Dispatcher $dispacher){
    //         $routes = config('route');
    //         if($routes){
    //             $router = $dispacher->getRouter();
    //             foreach($routes as $name => $route){
    //                 $router->addRoute($name, $route);
    //             }
    //         }
    //     }

    /**
     * 加载插件，放在最后执行
     *
     * @Author   liuchao
     *
     * @param \Yaf_Dispatcher $dispacher
     */
    public function _initPlugin(Yaf_Dispatcher $dispacher) {
        // 必须第一个注册的插件
        $dispacher->registerPlugin(new DispatchPlugin());
    }

}
