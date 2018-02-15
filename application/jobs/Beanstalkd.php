<?php

namespace Jobs;

abstract class Beanstalkd {

    public $connection;

    public $queue;

    public $ttl = 7;

    public function getLaterTime($ttl = 7) {
        return pow(2, $this->ttl - $ttl) * 2 * 60;
    }

    abstract public function push($data, $ttl = null);

    /**
     * 获取一个消息
     *
     * @return \Pheanstalk\Job
     *
     * @author  liuchao
     */
    public function pop() {
        return \Queue::onConnection($this->connection)->onQueue($this->queue)->pop();
    }

    public function delete($msg_id) {
        return \Queue::onConnection($this->connection)->onQueue($this->queue)->delete($msg_id);
    }

    public function size() {
        return \Queue::onConnection($this->connection)->onQueue($this->queue)->size();
    }

    public function fail($id) {

    }

    public function makeMessage($data, $ttl) {
        return json_encode([
            'job'  => $this->queue,
            'ttl'  => $ttl,
            'ts'   => time(),
            'data' => $data,
        ]);
    }
}