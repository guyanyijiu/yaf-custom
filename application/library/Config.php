<?php

/**
 * 获取配置项，获取时加载配置文件
 *
 * @Author   liuchao
 *
 * Class Config
 */

class Config implements ArrayAccess {

    /**
     * 配置文件所在目录
     *
     * @var null|string
     */
    protected $configPath;

    /**
     * 所有已加载的配置项
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Config constructor.
     *
     * @param null $configPath
     */
    public function __construct($configPath = null){
        $this->configPath = $configPath ? $configPath : ROOT_PATH . '/conf';
    }

    /**
     * 获取指定的配置项，如果不存在则返回$default
     *
     * @Author   liuchao
     *
     * @param      $key
     * @param null $default
     *
     * @return bool|mixed|null
     */
    public function get($key, $default = null){
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        if($value = $this->getConfiguration($key)){
            return $this->attributes[$key] = $value;
        }
        return value($default);
    }

    /**
     * 加载配置项
     *
     * @Author   liuchao
     *
     * @param null $name
     *
     * @return bool|mixed|null
     */
    private function getConfiguration($name = null){
        if (! $name) {
            return false;
        }

        if(strpos($name, '.')) {
            $names = explode('.', $name);
            $fileName = array_shift($names);
            $configs = $this->getFileConfiguration($fileName);

            if(! $configs){
                return false;
            }

            if(! $names){
                return $configs;
            }

            return array_reduce($names, function($carry, $item) {
                if(isset($carry[$item])){
                    return $carry[$item];
                }else{
                    return null;
                }
            }, $configs);

        }
        return $this->getFileConfiguration($name);
    }

    /**
     * 获取一个配置文件的所有数据
     *
     * @Author   liuchao
     *
     * @param $fileName
     *
     * @return mixed|null
     */
    private function getFileConfiguration($fileName){
        if (array_key_exists($fileName, $this->attributes)) {
            return $this->attributes[$fileName];
        }
        $configs = null;
        $iniConfigFile = $this->configPath . '/' . $fileName . '.ini';
        if(file_exists($iniConfigFile)){
            $configs = (new \Yaf_Config_Ini($iniConfigFile, YAF_ENVIRON))->toArray();
        }else{
            $phpConfigFile =  $this->configPath . '/' . $fileName . '.php';
            if(file_exists($phpConfigFile)){
                $configs = require $phpConfigFile;
            }
        }
        return $this->attributes[$fileName] = $configs;
    }

    /**
     * 对象转为数组
     *
     * @Author   liuchao
     * @return array
     */
    public function toArray(){
        return $this->attributes;
    }

    /**
     * 将对象转为 json
     *
     * @Author   liuchao
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0){
        return json_encode($this->toArray(), $options);
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    /**
     * Get the value for a given offset.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    /**
     * Unset the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }

    /**
     * 获取配置项
     *
     * @Author   liuchao
     *
     * @param $key
     *
     * @return bool|mixed|null
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * 设置配置项
     *
     * @Author   liuchao
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * 判断配置项
     *
     * @Author   liuchao
     *
     * @param $key
     *
     * @return bool
     */
    public function __isset($key){
        return isset($this->attributes[$key]);
    }

    /**
     * 删除配置项
     *
     * @Author   liuchao
     *
     * @param $key
     */
    public function __unset($key){
        unset($this->attributes[$key]);
    }
}
