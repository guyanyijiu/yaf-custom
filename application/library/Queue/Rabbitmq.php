<?php

namespace Queue;

use AMQPConnection;
use AMQPChannel;
use AMQPExchange;
use AMQPQueue;
use AMQPEnvelope;

/**
 * rabbitmq 连接类
 *
 * Class Rabbitmq
 *
 * @package Queue
 * @author  liuchao
 */
class Rabbitmq implements QueueInterface {

    /**
     * @var array
     */
    protected $config;

    /**
     * @var AMQPConnection
     */
    protected $connection;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @var AMQPExchange
     */
    protected $exchange;

    /**
     * @var AMQPQueue
     */
    protected $queue;


    /**
     * Rabbitmq constructor.
     *
     * @param $config
     */
    public function __construct($config) {
        $this->config = $config;

        $this->connect($config);
    }

    /**
     * 获取连接实例
     *
     * @param array $config
     *
     * @return $this|mixed
     *
     * @author  liuchao
     */
    public function connect(array $config) {
        $connection = new AMQPConnection([
            'host'     => $config['host'],
            'port'     => $config['port'],
            'login'    => $config['login'],
            'password' => $config['password'],
            'vhost'    => $config['vhost'],
        ]);

        try {
            $connection->pconnect();

            $this->connection = $connection;

            // 创建 channel
            $this->onChannel();

            // 创建 exchange
            $this->onExchange($config['exchange']);

            // 创建 queue
            $this->onQueue($config['queue'], $config['route_key']);
        } catch (\AMQPException $e) {
            $this->reconnection();
        }

        return $this;
    }

    /**
     * 重连
     *
     * @return $this|bool
     *
     * @author  liuchao
     */
    protected function reconnection() {
        $connection = new AMQPConnection([
            'host'     => $this->config['host'],
            'port'     => $this->config['port'],
            'login'    => $this->config['login'],
            'password' => $this->config['password'],
            'vhost'    => $this->config['vhost'],
        ]);

        try {
            $connection->pconnect();

            $this->connection = $connection;

            // 创建 channel
            $this->onChannel();

            // 创建 exchange
            $this->onExchange($this->config['exchange']);

            // 创建 queue
            $this->onQueue($this->config['queue'], $this->config['route_key']);
        } catch (\Throwable $e) {
            return false;
        }

        return $this;
    }

    /**
     * 设置 channel
     *
     * @return $this
     *
     * @author  liuchao
     */
    protected function onChannel() {
        $this->channel = new AMQPChannel($this->connection);
        $this->channel->basicRecover(true);

        return $this;
    }

    /**
     * 设置 exchange
     *
     * @param $name
     *
     * @return $this
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     *
     * @author  liuchao
     */
    protected function onExchange($name) {
        $exchange = new AMQPExchange($this->channel);
        $exchange->setName($name);
        // 路由模式
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        // 持久化
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();

        $this->exchange = $exchange;

        return $this;
    }

    /**
     * 设置 queue
     *
     * @param $name
     * @param $routeKey
     *
     * @return $this
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     *
     * @author  liuchao
     */
    protected function onQueue($name, $routeKey) {
        $queue = new AMQPQueue($this->channel);
        $queue->setName($name);
        $queue->setFlags(AMQP_DURABLE);

        $queue->declareQueue();

        $queue->bind($this->exchange->getName(), $routeKey);

        $this->queue = $queue;

        return $this;
    }

    /**
     * 执行入口
     *
     * @param \Closure $callback
     *
     * @return bool|mixed
     *
     * @author  liuchao
     */
    protected function run(\Closure $callback) {
        try {
            return $this->runExecuteCallback($callback);
        } catch (\AMQPException $e) {
            return $this->handleExecuteException($e, $callback);
        }
    }

    /**
     * 执行闭包
     *
     * @param \Closure $callback
     *
     * @return bool|mixed
     * @throws \AMQPException
     *
     * @author  liuchao
     */
    protected function runExecuteCallback(\Closure $callback) {
        try {
            return $callback();
        } catch (\AMQPException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \HandleExceptions::record($e);

            return false;
        }
    }

    /**
     * 处理执行异常
     *
     * @param          $e
     * @param \Closure $callback
     *
     * @return bool|mixed
     *
     * @author  liuchao
     */
    protected function handleExecuteException($e, \Closure $callback) {
        if ( !$this->reconnection()) {
            \HandleExceptions::record($e);

            return false;
        }
        try {
            return $this->runExecuteCallback($callback);
        } catch (\AMQPException $e) {
            \HandleExceptions::record($e);

            return false;
        }
    }

    /**
     * 发布一个消息
     *
     * @param $message
     *
     * @return bool|mixed
     *
     * @author  liuchao
     */
    public function push($message) {
        $exchange = $this->exchange;
        $routeKey = $this->config['route_key'];

        return $this->run(function () use ($exchange, $message, $routeKey) {
            return $exchange->publish($message, $routeKey);
        });

    }

    /**
     * 获取一个消息
     *
     * @return bool|mixed
     *
     * @author  liuchao
     */
    public function pop() {
        $queue = $this->queue;

        return $this->run(function () use ($queue) {
            return $queue->get();
        });
    }

    /**
     * 手动发送 ack
     *
     * @param AMQPEnvelope $envelope
     *
     * @return bool|mixed
     *
     * @author  liuchao
     */
    public function ack(AMQPEnvelope $envelope) {
        $queue = $this->queue;

        return $this->run(function () use ($queue, $envelope) {
            return $queue->ack($envelope->getDeliveryTag());
        });
    }

    /**
     * 手动发送 nack
     *
     * @param AMQPEnvelope $envelope
     *
     * @return bool|mixed
     *
     * @author  liuchao
     */
    public function nack(AMQPEnvelope $envelope) {
        $queue = $this->queue;

        return $this->run(function () use ($queue, $envelope) {
            return $queue->nack($envelope->getDeliveryTag());
        });
    }

}