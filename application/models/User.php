<?php

namespace Models;

class User extends \Model {

    /**
     * 未定义时使用配置文件中默认的连接
     *
     * @var string
     */
    public $connection = '';

    /**
     * 未定义时根据class名生成，例如 jaxApply 将对应表名 jax_apply
     *
     * @var string
     */
    public $table = '';

    /**
     * 根据ID获取一条数据
     *
     * @param $user_id
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function getUserById($user_id) {
        return $this->select('id', 'name', 'phone')->where('id', $user_id)->first();
    }

}