<?php

namespace Actions;

use Request;
use Response;
use Services\Api\LoginCheck;

class login extends \Base\Action {

    /**
     * 框架将执行此方法
     *
     * @param Request    $request
     * @param LoginCheck $loginCheck
     *
     * @return Response
     *
     * @author  liuchao
     */
    public function execute(\Request $request, LoginCheck $loginCheck) {
        $user_id = $request->get('user_id');

        $res = $loginCheck->check($user_id);

        if ( !$res) {
            return Response::fail($loginCheck->getError());
        }

        return Response::success();
    }

}