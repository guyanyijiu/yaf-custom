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


class Bootstrap extends Yaf_Bootstrap_Abstract{

    /**
     * 初始化设置
     *
     * @Author   liuchao
     *
     * @param \Yaf_Dispatcher $dispatcher
     */
    public function _initConfig(Yaf_Dispatcher $dispatcher){
        if(YAF_ENVIRON == 'product'){
            error_reporting(0);
        }else{
            error_reporting(E_ALL);
        }
    }

    /**
     *  生成唯一请求ID
     *
     * @Author   liuchao
     *
     * @param \Yaf_Dispatcher $dispatcher
     */
    public function _initRequestId(Yaf_Dispatcher $dispatcher){
        // 先获取传递的requestId
        $requestId = $dispatcher->getRequest()->getServer('REQUEST_ID');
        if(! $requestId){
            $serverIp = $dispatcher->getRequest()->getServer('SERVER_ADDR');
            $requestId = str_replace('.', '', sprintf('%.6F', YAF_START)) . ip2long($serverIp) . mt_rand(1000, 9999);
        }

        Yaf_Registry::set('_requestId', $requestId);
    }

    /**
     * 引入composer自动加载
     *
     * @Author   liuchao
     */
    public function _initComposer(){
        Yaf_Loader::import(ROOT_PATH . '/vendor/autoload.php');
    }

    /**
     * 引入辅助函数
     *
     * @Author   liuchao
     */
    public function _initHelpers(){
        Yaf_Loader::import(ROOT_PATH . '/helper/functions.php');
        Yaf_Loader::import(ROOT_PATH . '/helper/helpers.php');
    }

    /**
     * 加载容器，注册类库
     *
     * @Author   liuchao
     */
    public function _initContainer(){

        // 实例化一个容器对象
        $container = new Pimple\Container();

        // 注册config
        $container['config'] = function (){
            return new Config();
        };

        // 注册db
        $container->register(new \guyanyijiu\Database\DatabaseServiceProvider());

        // 注册Redis
        $container->register(new \guyanyijiu\Redis\RedisServiceProvider());

        Yaf_Registry::set('container', $container);
    }

    /**
     * 加载配置文件中自定义路由
     *
     * @Author   liuchao
     *
     * @param \Yaf_Dispatcher $dispacher
     */
     public function _initRoute(Yaf_Dispatcher $dispacher){
         $routes = config('route');
         if($routes){
             $router = $dispacher->getRouter();
             foreach($routes as $name => $route){
                 $router->addRoute($name, $route);
             }
         }
     }

    /**
     * 加载插件
     *
     * @Author   liuchao
     *
     * @param \Yaf_Dispatcher $dispacher
     */
    public function _initPlugin(Yaf_Dispatcher $dispacher){
         $dispacher->registerPlugin(new TestPlugin());
         $dispacher->registerPlugin(new RequestPlugin());
    }

}
