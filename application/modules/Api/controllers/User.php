<?php


class UserController extends \Controller\Api {

    public function getAction(){
        DB::connection('lumen');
        $info = DB::table('test')->where('id', 1)->first();
        $info = DB::connection('lumen')->select('select * from user where name = ?', ['admin']);
        $info = \Api\UserModel::where('id', 1)->get();
        dump($info);
    }
}
