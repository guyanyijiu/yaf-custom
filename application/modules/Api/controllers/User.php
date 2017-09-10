<?php


class UserController extends \Controller\Api {

    public function getAction(){
//        var_dump(YAF_ENVIRON);exit;
        $data = \Api\JaxApplyCompanyModel::where('company_id', '<', 20)->first();
        var_dump($data);
        $user = new \Api\UserModel();
        var_dump($user->get(2));
        $user = \Api\UserModel::where('id', 1)->first();
        var_dump($user);
        container('db')::transaction(function(){
            \Api\UserModel::where('id', 1)->update(['name' => 'liuchafdfdofffhaha']);
        });
    }
}
