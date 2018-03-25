<?php

namespace Services\Api;

use Models\User;

class LoginCheck extends \Base\Service {

    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function check($user_id) {
        $user = $this->user->getUserById($user_id);
        if ($user->id == 1) {
            return true;
        }

        /**
         * 给调用者返回错误信息
         */
        $this->setError('未找到用户');

        return false;
    }
}