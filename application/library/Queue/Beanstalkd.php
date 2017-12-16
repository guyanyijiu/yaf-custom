<?php

namespace Queue;

use Pheanstalk\Exception\ServerException;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Connection;
use Pheanstalk\Job;
use Pheanstalk\PheanstalkInterface;

class Beanstalkd {

    /**
     * Pheanstalk 连接实例
     *
     * @var Pheanstalk
     */
    protected $pheanstalk;

    /**
     * 默认 tube
     *
     * @var string
     */
    protected $default;

    /**
     * 默认 ttr
     *
     * @var int
     */
    protected $timeToRun;

    /**
     * 当前连接的配置
     *
     * @var array
     */
    protected $config;


    /**
     * Beanstalkd constructor.
     *
     * @param $config
     */
    public function __construct($config) {
        $this->config = $config;

        $this->default = $config['queue'];
        $this->timeToRun = $config['retry_after'];

        $this->pheanstalk = $this->connect($config);
    }

    /**
     * 建立连接
     *
     * @param array $config
     *
     * @return Pheanstalk
     *
     * @author  liuchao
     */
    protected function connect(array $config) {
        return new Pheanstalk(
            $config['host'],
            $config['port'] ?: PheanstalkInterface::DEFAULT_PORT,
            $config['timeout'] ?: Connection::DEFAULT_CONNECT_TIMEOUT,
            $config['persistent'] ?: false
        );
    }

    /**
     * 设置 tube
     *
     * @param null $name
     *
     * @return $this
     *
     * @author  liuchao
     */
    public function onQueue($name = null) {
        if ( !is_null($name)) {
            $this->default = $name;
        }

        return $this;
    }

    /**
     * 获取当前队列大小
     *
     * @param null $queue
     *
     * @return int
     *
     * @author  liuchao
     */
    public function size($queue = null) {
        $queue = $this->getQueue($queue);

        return (int) $this->pheanstalk->statsTube($queue)->current_jobs_ready;
    }


    /**
     * 推送一个消息到队列
     *
     * @param $message
     *
     * @return int
     *
     * @author  liuchao
     */
    public function push($message) {
        return $this->pushRaw(
            $this->createPayload($message),
            Pheanstalk::DEFAULT_PRIORITY,
            Pheanstalk::DEFAULT_DELAY,
            $this->timeToRun
        );
    }


    /**
     * 推送消息到队列
     *
     * @param     $payload
     * @param int $priority
     * @param int $delay
     * @param int $ttr
     *
     * @return int
     *
     * @author  liuchao
     */
    public function pushRaw($payload, $priority = Pheanstalk::DEFAULT_PRIORITY, $delay = Pheanstalk::DEFAULT_DELAY, $ttr = Pheanstalk::DEFAULT_TTR) {
        return $this->pheanstalk->useTube($this->getQueue())->put(
            $payload, $priority, $delay, $ttr
        );
    }

    /**
     * 推送一个延迟消息到队列
     *
     * @param $message
     * @param $delay
     *
     * @return int
     *
     * @author  liuchao
     */
    public function later($message, $delay) {
        return $this->pushRaw(
            $this->createPayload($message),
            Pheanstalk::DEFAULT_PRIORITY,
            $delay,
            $this->timeToRun
        );
    }

    /**
     * 取出一个消息
     *
     * @param null $queue
     *
     * @return bool|object|Job
     *
     * @author  liuchao
     */
    public function pop($queue = null) {
        $queue = $this->getQueue($queue);

        $job = $this->pheanstalk->watchOnly($queue)->reserve(0);

        return $job;
    }

    /**
     * 阻塞式的取出消息
     *
     * @return bool|object|Job
     *
     * @author  liuchao
     */
    public function reserve() {
        return $this->pheanstalk->watchOnly($this->getQueue())->reserve();
    }

    /**
     * 删除一个消息
     *
     * @param      $id
     * @param null $queue
     *
     * @author  liuchao
     */
    public function delete($id, $queue = null) {
        $queue = $this->getQueue($queue);

        try {
            $this->pheanstalk->useTube($queue)->delete(new Job($id, ''));
        } catch (ServerException $e) {

        }
    }

    /**
     * 处理消息
     *
     * @param $message
     *
     * @return string
     *
     * @author  liuchao
     */
    public function createPayload($message) {
        if (is_array($message)) {
            return json_encode($message);
        }
        if (is_string($message)) {
            return $message;
        }
        if (is_object($message)) {
            return serialize($message);
        }
    }

    /**
     * 获取 tube 名
     *
     * @param null $queue
     *
     * @return string
     *
     * @author  liuchao
     */
    public function getQueue($queue = null) {
        return $queue ?: $this->default;
    }

    /**
     * 获取 Pheanstalk 实例
     *
     * @return Pheanstalk
     *
     * @author  liuchao
     */
    public function getPheanstalk() {
        return $this->pheanstalk;
    }
}
