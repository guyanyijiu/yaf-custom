<?php

/**
 * 获取配置项, 获取时才加载配置文件, 不允许运行中修改配置
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
     * 所有缓存的配置项
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * 所有缓存的配置文件
     *
     * @var array
     */
    protected $configFiles = [];

    /**
     * Config constructor.
     *
     * @param $configPath
     */
    public function __construct($configPath) {
        $this->configPath = $configPath;
    }

    /**
     * 获取指定的配置项，如果不存在则返回 $default
     *
     * @param      $name
     * @param null $default
     * @param bool $use_cache
     *
     * @return array|mixed|null
     * @author  liuchao
     */
    public function get($name, $default = null, $use_cache = true) {
        if ($use_cache && isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        $pos = strpos($name, '.');
        $file = $pos ? substr($name, 0, $pos) : $name;

        if ($file == 'app') {
            $file = 'application';
        }

        $configs = $this->getFileConfiguration($file);

        if ( !$configs) {
            return $default;
        }

        if ($pos) {
            $key = substr($name, $pos + 1);
            if ($configs instanceof \Yaf_Config_Ini) {
                $value = $configs->get($key);
                if (is_null($value)) {
                    return $default;
                }
            } else {
                $key = explode('.', $key);
                $value = $configs;
                foreach ($key as $v) {
                    $value = $value->get($v);
                    if (is_null($value)) {
                        return $default;
                    }
                }
            }
            $value = $value instanceof \Yaf_Config_Abstract ? $value->toArray() : $value;

        } else {
            $value = $configs->toArray();
        }

        $this->attributes[$name] = $value;

        return $value;
    }

    /**
     * 获取一个配置文件的所有数据
     *
     * @Author   liuchao
     *
     * @param      $fileName
     *
     * @return null|Yaf_Config_Abstract
     */
    private function getFileConfiguration($fileName) {
        if (isset($this->configFiles[$fileName])) {
            return $this->configFiles[$fileName];
        }

        $configs = null;
        $iniConfigFile = $this->configPath . '/' . $fileName . '.ini';
        if (file_exists($iniConfigFile)) {
            $configs = new \Yaf_Config_Ini($iniConfigFile, YAF_ENVIRON);
        } else {
            $phpConfigFile = $this->configPath . '/' . $fileName . '.php';
            if (file_exists($phpConfigFile)) {
                $configs = new \Yaf_Config_Simple(require $phpConfigFile);
            }
        }

        $this->configFiles[$fileName] = $configs;

        return $configs;
    }

    /**
     * 设置一个配置项
     *
     * @param $key
     * @param $value
     *
     * @author  liuchao
     */
    public function set($key, $value) {
        $this->attributes[$key] = $value;
    }

    /**
     * 对象转为数组
     *
     * @Author   liuchao
     * @return array
     */
    public function toArray() {
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
    public function toJson($options = 0) {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  string $offset
     *
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->attributes[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string $offset
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    /**
     * Unset the value at the given offset.
     *
     * @param  string $offset
     *
     * @return void
     */
    public function offsetUnset($offset) {
        unset($this->attributes[$offset]);
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
    public function __get($key) {
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
    public function __set($key, $value) {
        $this->set($key, $value);
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
    public function __isset($key) {
        return isset($this->attributes[$key]);
    }

    /**
     * 删除配置项
     *
     * @Author   liuchao
     *
     * @param $key
     */
    public function __unset($key) {
        unset($this->attributes[$key]);
    }
}
