<?php

use Monolog\Logger;
use Monolog\Handler\BufferHandler;
use Monolog\Formatter\LineFormatter;
use guyanyijiu\Log\AggregateFileHandler;

/**
 * 日志类
 *
 * @Author   liuchao
 *
 * Class Log
 */
class Log {
    /**
     * monolog 实例
     *
     * @var array
     */
    protected static $loggers = [];

    /**
     * 根据模块名生成不同的monolog实例
     *
     * @Author   liuchao
     * @return mixed|\Monolog\Logger
     */
    public static function getLogger(){
        $moduleName = Yaf_Dispatcher::getInstance()->getRequest()->getModuleName();

        if(isset(static::$loggers[$moduleName])){
            return static::$loggers[$moduleName];
        }

        // > info 的日志
        $fileUpInfo = new AggregateFileHandler(config('application.logPath') . config('application.appName') . '/' . $moduleName . '-' . date('Y-m-d') . '.log', Logger::INFO);
        $fileUpInfo->setFormatter(
            new LineFormatter(Yaf_Registry::get('_requestId') . " | %datetime% | %channel% | %level_name% | %message% | %context%\n", 'Y-m-d H:i:s.u')
        );
        $fileUpInfoBuffer = new BufferHandler($fileUpInfo, 0, Logger::INFO, false);

        // > debug 的日志
        $fileUpDebug = new AggregateFileHandler(config('application.logPath') . config('application.appName') . '/debug-' . $moduleName . '-' . date('Y-m-d') . '.log', Logger::DEBUG);
        $fileUpDebug->setFormatter(
            new LineFormatter(Yaf_Registry::get('_requestId') . " | %datetime% | %channel% | %level_name% | %message% | %context%\n", 'Y-m-d H:i:s.u')
        );
        $fileUpDebugBuffer = new BufferHandler($fileUpDebug, 0, Logger::DEBUG, false);

        $logger = new Logger($moduleName);
        $logger->pushHandler($fileUpDebugBuffer);
        $logger->pushHandler($fileUpInfoBuffer);

        return static::$loggers[$moduleName] = $logger;
    }

    /**
     * 代理普通方法调用
     *
     * @Author   liuchao
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters){
        if(YAF_ENVIRON == 'product' && strtolower($method) == 'debug'){
            return;
        }
        return call_user_func_array([static::getLogger(), $method], $parameters);
    }

    /**
     * 代理静态方法调用
     *
     * @Author   liuchao
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters){
        if(YAF_ENVIRON == 'product' && strtolower($method) == 'debug'){
            return;
        }
        return call_user_func_array([static::getLogger(), $method], $parameters);
    }

}
