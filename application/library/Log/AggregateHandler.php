<?php

namespace Log;

use Monolog\Handler\StreamHandler;

/**
 * 日志相关类，请求结束将日志一次性写入文件
 *
 * @Author   liuchao
 *
 * Class AggregateFileHandler
 */
class AggregateHandler extends StreamHandler {

    public function handleBatch(array $records) {

        // 格式化日志
        $log = '';
        foreach ($records as $record) {
            if ( !$this->isHandling($record)) {
                continue;
            }
            $record = $this->processRecord($record);
            $log .= $this->getFormatter()->format($record);
        }

        // 调用日志写入方法
        $this->write(['formatted' => $log]);
    }

}