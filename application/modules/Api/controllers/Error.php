<?php

/**
 * 当有未捕获的异常, 则控制流会流到这里
 */
class ErrorController extends Yaf_Controller_Abstract{
    /**
     * 也可通过$request->getException()获取到发生的异常
     */
    public function errorAction($exception){
        var_dump('api',$exception);exit;
        switch ($exception->getCode()) {
            case YAF_ERR_LOADFAILD:
                var_dump('exception');
                break;

            case YAF_ERR_LOADFAILD_MODULE:
            case YAF_ERR_LOADFAILD_CONTROLLER:
            case YAF_ERR_LOADFAILD_ACTION:
                //404
                header("Not Found");
                break;

            case CUSTOM_ERROR_CODE:
                //自定义的异常

                break;

        }
    }

}
