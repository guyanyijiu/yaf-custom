<?php

namespace Actions;

use Request;
use Response;
use Services\Api\LoginCheck;

class login extends \Action {

    protected $loginCheck;

    public function __construct(LoginCheck $loginCheck) {
        $this->loginCheck = $loginCheck;
    }

    /**
     * 框架将执行此方法
     *
     *
     * @author  liuchao
     */
    public function execute() {
        $user_id = Request::get('user_id');

        $res = $this->loginCheck->check($user_id);

        if ( !$res) {
            Response::fail($this->loginCheck->getError());
        }

        Response::success();
    }

}