<?php


class OrderController extends \Controller\Api {

    public function getAction(){

        dump(app()->getDispatcher()->getRouter()->getCurrentRoute());
        dump($this->getRequest()->getParam('id'));
    }
}
