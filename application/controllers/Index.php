<?php

class IndexController extends Yaf_Controller_Abstract {

    public function indexAction(){
        exit(json_encode(["hello world !"]));
    }

}