<?php

class HandleExceptions {


    /**
     * 注册异常处理
     *
     *
     * @author  liuchao
     */
    public static function register() {

        error_reporting(-1);

        set_error_handler([static::class, 'handleError']);

        set_exception_handler([static::class, 'handleException']);

        register_shutdown_function([static::class, 'handleShutdown']);

        if (YAF_ENVIRON == 'product') {
            ini_set('display_errors', 'Off');
        }
    }

    /**
     * 把 PHP error 转为 ErrorException
     *
     * @param        $level
     * @param        $message
     * @param string $file
     * @param int    $line
     *
     * @throws ErrorException
     *
     * @author  liuchao
     */
    public static function handleError($level, $message, $file = '', $line = 0) {
        if (error_reporting() & $level) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * 处理程序未捕获的异常
     *
     * @param Throwable $e
     *
     * @author  liuchao
     */
    public static function handleException(\Throwable $e) {
        if ($e instanceof \Yaf_Exception) {
            $type = 'YAF 异常';
        } elseif ($e instanceof \PDOException) {
            $type = 'POD 异常';
        } else {
            $type = '未知 异常';
        }

        Log::exception($type, [
            'code'    => $e->getCode(),
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        if (PHP_SAPI == 'cli') {
            static::renderForConsole($e);
        } else {
            static::renderHttpResponse($e);
        }
    }

    /**
     *  cli 输出
     *
     * @param $e
     *
     * @author  liuchao
     */
    protected static function renderForConsole($e) {
        throw $e;
    }

    /**
     * http 输出
     *
     * @param Exception $e
     *
     * @author  liuchao
     */
    protected static function renderHttpResponse($e) {
        Response::fail('程序内部错误');
    }

    /**
     * 处理 PHP 异常退出
     *
     *
     * @author  liuchao
     */
    public static function handleShutdown() {
        if ( !is_null($error = error_get_last()) && static::isFatal($error['type'])) {
            static::handleException(
                new \ErrorException(
                    $error['message'], $error['type'], 0, $error['file'], $error['line']
                )
            );
        }
    }

    /**
     * 判断是否是 Fatal Error
     *
     * @param $type
     *
     * @return bool
     *
     * @author  liuchao
     */
    protected static function isFatal($type) {
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }

}
