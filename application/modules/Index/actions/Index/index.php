<?php

namespace Actions;

class index extends \Base\Action {

    public function execute(\Request $request){
        return ['hello world !'];
    }

}