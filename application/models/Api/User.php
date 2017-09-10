<?php
namespace Api;

class UserModel extends \Model {
    public $connection = 'lumen';

    public function get($id){
        return $this->where('id', $id)->first();
    }
}
