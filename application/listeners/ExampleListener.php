<?php

namespace Listeners;

use Events\ExampleEvent;

/**
 * 定义事件监听者
 *
 * Class ExampleListener
 *
 * @package Listeners
 * @author  liuchao
 */
class ExampleListener {

    /**
     * 事件触发会调用该方法
     *
     * @param ExampleEvent $event
     *
     * @author  liuchao
     */
    public function handle(ExampleEvent $event) {
        $data = $event->data;
    }
}