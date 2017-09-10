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
     * 初始化一些设置
     *
     * @Author   liuchao
     */
    public function _initConfig(){
        if(YAF_ENVIRON == 'product'){
            error_reporting(0);
        }else{
            error_reporting(E_ALL);
        }
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

        //向容器中注册config
        $container['config'] = function (){
            return new Config();
        };

        //向容器中注册db
        $container['db.factory'] = function (){
            return new \guyanyijiu\Database\Connectors\ConnectionFactory();
        };
        $container['db'] = function () use ($container){
            return new \guyanyijiu\Database\DatabaseManager($container, $container['db.factory']);
        };

        \guyanyijiu\Database\Model::setConnectionResolver($container['db']);

        Yaf_Registry::set('container', $container);
    }

    /**
     * 加载插件
     *
     * @Author   liuchao
     *
     * @param \Yaf_Dispatcher $dispacher
     */
     public function _initPlugin(Yaf_Dispatcher $dispacher){
//         $dispacher->registerPlugin(new TestPlugin());
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

}
