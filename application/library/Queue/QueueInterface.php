<?php

namespace Queue;

interface QueueInterface {
    /**
     * 连接
     *
     * @param array $config
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function connect(array $config);
}