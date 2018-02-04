<?php

namespace Listeners;

/**
 * 在一个类里监听多个事件
 *
 * Class ExampleSubscriber
 *
 * @package Listeners
 * @author  liuchao
 */
class ExampleSubscriber {

    /**
     * 事件1的处理方法
     *
     * @param $event
     *
     * @author  liuchao
     */
    public function handleEventName1($event) {

    }

    /**
     * 事件2的处理方法
     *
     * @param $event
     *
     * @author  liuchao
     */
    public function handleEventName2($event) {

    }

    /**
     * 定义该方法注册监听者
     *
     * @param $events
     *
     * @author  liuchao
     */
    public function subscribe($events) {
        $events->listen(
            'Events\ExampleEvent',
            'Listeners\ExampleSubscriber@handleEventName1'
        );

        $events->listen(
            'Events\ExampleEvent',
            'Listeners\ExampleSubscriber@handleEventName2'
        );
    }
}