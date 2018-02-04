<?php

/**
 * 异常处理类
 *
 * Class HandleExceptions
 *
 * @author  liuchao
 */
class HandleExceptions {


    /**
     * 注册异常处理
     *
     *
     * @author  liuchao
     */
    public static function register() {

        if (YAF_ENVIRON == 'product') {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        } else {
            error_reporting(-1);
        }

        set_error_handler([static::class, 'handleError']);

        set_exception_handler([static::class, 'handleException']);

        register_shutdown_function([static::class, 'handleShutdown']);
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
        try {
            static::record($e);
        } catch (\Throwable $e) {

        }

        if (PHP_SAPI == 'cli') {
            static::renderForConsole($e);
        } else {
            static::renderForHttp($e);
        }
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
     * cli 输出
     *
     * @param Throwable $e
     *
     * @author  liuchao
     */
    protected static function renderForConsole(\Throwable $e) {
        echo $e->getCode(), "\n", $e->getMessage(), "\n", $e->getTraceAsString(), "\n";
    }

    /**
     * http 输出
     *
     * @param Throwable $e
     *
     * @author  liuchao
     */
    protected static function renderForHttp(\Throwable $e) {
        if (YAF_ENVIRON == 'product') {
            $message = '程序内部错误';
        } else {
            $message = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
        }

        \Response::fail($message)->send();
    }

    /**
     * 生成一个异常响应
     *
     * @param Throwable $e
     *
     * @return Response
     * @throws Exception
     *
     * @author  liuchao
     */
    public static function makeExceptionResponse(\Throwable $e) {
        if (YAF_ENVIRON == 'product') {
            $message = '程序内部错误';
        } else {
            $message = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
        }

        static::record($e);

        return \Response::fail($message);
    }

    /**
     * 记录异常日志
     *
     * @param Throwable $e
     *
     * @throws Exception
     *
     * @author  liuchao
     */
    public static function record(\Throwable $e) {
        \Log::exception('未捕获异常', [
            'code'    => $e->getCode(),
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);
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
