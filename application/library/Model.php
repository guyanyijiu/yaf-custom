<?php

/**
 * Model 基类
 *
 * @Author   liuchao
 * Class Model
 */

class Model extends \guyanyijiu\Database\Model {

    /**
     * @var mixed 当前模型对应的数据库表名(取类名)
     */
    protected $table;

    public function __construct(){
        if(! $this->table){
            $class_name = get_class($this);
            if(($pos = strrpos($class_name, '\\')) !== false){
                $class_name = substr($class_name, $pos + 1);
            }
            $class_name = str_replace('Model', '', $class_name);
            $this->table = $class_name;
        }

        parent::__construct();
    }
}
