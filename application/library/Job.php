<?php

class Job {

    private static $instances = [];

    /**
     * 路由消息
     *
     * @param $jobName
     *
     * @return \Jobs\Beanstalkd
     *
     * @author  liuchao
     */
    public static function dispatch($jobName) {
        $jobName = "\\Jobs\\" . ucfirst(strtolower($jobName));
        if ( !isset(self::$instances[$jobName])) {
            if ( !class_exists($jobName)) {
                throw new InvalidArgumentException($jobName . ' Class not found');
            }
            self::$instances[$jobName] = new $jobName;
        }

        return self::$instances[$jobName];
    }

}