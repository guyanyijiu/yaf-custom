<?php


class UserController extends \Controller\Api {

    public function getAction(){
        dump($this->getRequest()->getParam('id'));
    }
}
