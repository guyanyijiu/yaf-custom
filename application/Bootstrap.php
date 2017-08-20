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

use Illuminate\Database\Capsule\Manager as Capsule;

class Bootstrap extends Yaf_Bootstrap_Abstract{

    /**
     * 引入composer自动加载
     *
     * @Author   liuchao
     */
    public function _initComposer(){
        Yaf_Loader::import(APP_PATH . '/vendor/autoload.php');
    }

    /**
     * 加载配置文件
     *
     * @Author   liuchao
     */
    public function _initConfig(){

        $files = scandir(APP_PATH . '/conf/');
        foreach($files as $file){
            if(preg_match('/^(\w+)\.(ini|php)$/', $file, $matches)){
                $file = APP_PATH . '/conf/' . $file;
                switch ($matches[2]) {
                    case 'ini':
                        if($matches[1] == 'application'){
                            $matches[1] = 'app';
                        }
                        $configs[$matches[1]] = (new Yaf_Config_Ini($file, YAF_ENVIRON))->toArray();
                        break;
                    case 'php':
                        // $configs[$matches[1]] = (new Yaf\Config\Simple(include $file)) -> toArray();
                        $configs[$matches[1]] = include $file;
                        break;
                }
            }
        }
        Yaf_Registry::set('configs', $configs);
    }

    /**
     * 引入辅助函数
     *
     * @Author   liuchao
     */
    public function _initHelpers(){
        Yaf_Loader::import(APP_PATH . '/helper/functions.php');
        Yaf_Loader::import(APP_PATH . '/helper/helpers.php');
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
     * 引入 Eloquent
     *
     * @Author   liuchao
     */
     public function _initDatabase(){
         $configs = config('database');
         if($configs){
             $capsule = new Capsule;
             if(! $default = config('database.default')){
                 $default = array_shift($configs);
             }
             $capsule->addConnection($default);
             $capsule->setAsGlobal();
             $capsule->bootEloquent();
             Yaf_Registry::set('Capsule', $capsule);
         }
     }


}
