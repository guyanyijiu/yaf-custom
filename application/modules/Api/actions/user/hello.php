<?php

class helloAction extends \Action {

    public function execute(){
        $user = new Api\UserModel();
        echo "hello {$user->get()} !";
    }
}