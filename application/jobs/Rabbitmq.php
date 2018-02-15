<?php

namespace Jobs;

/**
 * 事件消息类
 *
 * Class Rabbitmq
 *
 * @package Jobs
 * @author  liuchao
 */
abstract class Rabbitmq {

    /**
     * 使用的消息队列连接名
     *
     * @var string
     */
    protected static $connection;

    /**
     * 生成消息体
     *
     * @param $id
     * @param $event_no
     * @param $data
     *
     * @return string
     *
     * @author  liuchao
     */
    protected static function makeMessage($id, $event_no, $data) {
        return json_encode([
            'id'       => $id,
            'event_no' => $event_no,
            'data'     => $data,
        ]);
    }

    /**
     * 发布一个消息
     *
     * @param $id
     * @param $event_no
     * @param $data
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public static function push($id, $event_no, $data) {
        $message = static::makeMessage($id, $event_no, $data);

        return \Queue::onConnection(static::$connection)->push($message);
    }

    /**
     * 获取一个消息
     *
     * @return false | \AMQPEnvelope
     *
     * @author  liuchao
     */
    public static function pop() {
        return \Queue::onConnection(static::$connection)->pop();
    }

    /**
     * 发送 ack
     *
     * @param \AMQPEnvelope $message
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public static function ack(\AMQPEnvelope $message) {
        return \Queue::onConnection(static::$connection)->ack($message);
    }

    /**
     * 发送 nack
     *
     * @param \AMQPEnvelope $message
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public static function nack(\AMQPEnvelope $message) {
        return \Queue::onConnection(static::$connection)->nack($message);
    }
}