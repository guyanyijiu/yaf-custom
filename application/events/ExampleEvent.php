<?php

namespace Events;

/**
 * 定义事件
 *
 * Class ExampleEvent
 *
 * @package Events
 * @author  liuchao
 */
class ExampleEvent {

    public $data;

    public function __construct($data) {
        $this->data = $data;
    }
}