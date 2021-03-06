<?php

use Monolog\Logger;
use Monolog\Handler\BufferHandler;
use Monolog\Formatter\LineFormatter;
use Log\AggregateFileHandler;
use Log\AggregateCliFileHandler;

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
     * @var Logger
     */
    protected static $logger;

    /**
     * sql 日志的 monolog 实例
     *
     * @var Logger
     */
    protected static $special_loggers = [];

    /**
     * 当前日期
     *
     * @var string
     */
    protected static $date;

    /**
     * 根据请求参数生成不同的monolog实例
     *
     * @return Logger
     * @throws Exception
     *
     * @author  liuchao
     */
    protected static function getLogger() {

        if (is_null(static::$logger)) {
            $request = container(\Request::class);
            $moduleName = $request->getModule();
            $controllerName = $request->getController();

            $logFile = config('application.log_path') . '/' . config('application.app_name') . '/' . $moduleName . '/' . $controllerName . '/' . date('Y-m-d') . '.log';
            $fileHandler = new AggregateFileHandler($logFile, Logger::DEBUG);
            $fileHandler->setFormatter(
                new LineFormatter(\Uniqid::getRequestId() . "|%datetime%|%channel%|%level_name%|%message%|%context%|%extra%\n", 'Y-m-d H:i:s.u')
            );
            $bufferHandler = new BufferHandler($fileHandler, 100, Logger::DEBUG, false, true);

            $logger = new Logger($moduleName);
            $logger->pushHandler($bufferHandler);

            static::$logger = $logger;
        }

        return static::$logger;
    }

    /**
     * 生成命令行下的 monolog 实例
     *
     * @return Logger
     * @throws Exception
     *
     * @author  liuchao
     */
    protected static function getCliLogger() {
        if (is_null(static::$date) || static::$date != date('Y-m-d')) {
            static::$date = date('Y-m-d');
            static::$logger = null;
        }

        if (is_null(static::$logger)) {
            $script = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/') + 1, -4);
            $logFile = config('application.log_path') . '/' . config('application.app_name') . '/cli/' . $script . '/' . date('Y-m-d') . '.log';
            $fileHandler = new AggregateCliFileHandler($logFile, Logger::DEBUG);
            $fileHandler->setFormatter(
                new LineFormatter(Uniqid::getRequestId() . "|%datetime%|%channel%|%level_name%|%message%|%context%|%extra%\n", 'Y-m-d H:i:s.u')
            );

            $logger = new Logger($script);
            $logger->pushHandler($fileHandler);

            static::$logger = $logger;
        }

        return static::$logger;
    }

    /**
     * 获取一个特殊类型的 monolog 实例
     *
     * @param      $type
     * @param bool $is_buffer
     *
     * @return mixed
     * @throws Exception
     *
     * @author  liuchao
     */
    protected static function getSpecialLogger($type, $is_buffer = false) {
        if (is_null(static::$date) || static::$date != date('Y-m-d')) {
            static::$date = date('Y-m-d');
            static::$special_loggers = [];
        }

        if ( !isset(static::$special_loggers[$type])) {
            if (PHP_SAPI == 'cli') {
                $is_buffer = false;
                $moduleName = 'cli';
                $controllerName = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/') + 1, -4);
            } else {
                $request = container(\Request::class);
                $moduleName = $request->getModule();
                $controllerName = $request->getController();
            }

            $logFile = config('application.log_path') . '/' . config('application.app_name') . '/' . $moduleName . '/' . $controllerName . '/' . $type . '.' . date('Y-m-d') . '.log';

            if ($is_buffer) {
                $fileHandler = new \Log\AggregateHandler($logFile, Logger::DEBUG);
                $fileHandler->setFormatter(
                    new LineFormatter(\Uniqid::getRequestId() . "|%datetime%|%channel%|$type|%message%|%context%\n", 'Y-m-d H:i:s.u')
                );
                $handler = new BufferHandler($fileHandler, 100, Logger::DEBUG, false, true);
            } else {
                $fileHandler = new \Monolog\Handler\StreamHandler($logFile, Logger::DEBUG);
                $fileHandler->setFormatter(
                    new LineFormatter(Uniqid::getRequestId() . "|%datetime%|%channel%|$type|%message%|%context%\n", 'Y-m-d H:i:s.u')
                );
                $handler = $fileHandler;
            }

            $logger = new Logger($moduleName);
            $logger->pushHandler($handler);

            static::$special_loggers[$type] = $logger;
        }

        return static::$special_loggers[$type];
    }

    /**
     * 记录未捕获异常的日志
     *
     * @param       $message
     * @param array $context
     *
     * @throws Exception
     *
     * @author  liuchao
     */
    public static function exception($message, array $context = []) {
        if ( !$message) {
            return;
        }
        $logger = static::getSpecialLogger('exception', false);

        $logger->info($message, $context);
    }

    /**
     * 记录 SQL 日志
     *
     * @param $logs
     *
     * @throws Exception
     *
     * @author  liuchao
     */
    public static function sql($logs) {
        if ( !$logs) {
            return;
        }
        $logger = static::getSpecialLogger('sql', true);

        foreach ($logs as $v) {
            $logger->info($v['time'] . '|' . $v['query'], $v['bindings']);
        }
    }

    public static function debug($message, array $context = []) {
        $logger = PHP_SAPI == 'cli' ? static::getCliLogger() : static::getLogger();

        return $logger->debug($message, $context);
    }

    public static function info($message, array $context = []) {
        $logger = PHP_SAPI == 'cli' ? static::getCliLogger() : static::getLogger();

        return $logger->info($message, $context);
    }

    public static function warn($message, array $context = []) {
        $logger = PHP_SAPI == 'cli' ? static::getCliLogger() : static::getLogger();

        return $logger->warn($message, $context);
    }

    public static function error($message, array $context = []) {
        $logger = PHP_SAPI == 'cli' ? static::getCliLogger() : static::getLogger();

        return $logger->error($message, $context);
    }

    /**
     * 代理普通方法调用
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     * @throws Exception
     *
     * @author  liuchao
     */
    public function __call($method, $parameters) {
        $logger = PHP_SAPI == 'cli' ? static::getCliLogger() : static::getLogger();

        return $logger->{$method}(...$parameters);
    }

    /**
     * 代理静态方法调用
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     * @throws Exception
     *
     * @author  liuchao
     */
    public static function __callStatic($method, $parameters) {
        $logger = PHP_SAPI == 'cli' ? static::getCliLogger() : static::getLogger();

        return $logger->{$method}(...$parameters);
    }

}
