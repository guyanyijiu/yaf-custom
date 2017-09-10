<?php


class Config implements ArrayAccess
{
    protected $configPath;

    protected $attributes = [];

    /**
     * Config constructor.
     *
     * @param null $configPath
     */
    public function __construct($configPath = null){
        $this->configPath = $configPath ? $configPath : ROOT_PATH . '/conf';
    }


    public function get($key, $default = null){
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        if($value = $this->getConfiguration($key)){
            return $this->attributes[$key] = $value;
        }
        return value($default);
    }

    public function getConfiguration($name = null)
    {
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
    public function getFileConfiguration($fileName){
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
     * Get the attributes from the container.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Convert the Fluent instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the Fluent instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
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
     * Handle dynamic calls to the container to set attributes.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;

        return $this;
    }

    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Dynamically set the value of an attribute.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Dynamically check if an attribute is set.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Dynamically unset an attribute.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }
}
