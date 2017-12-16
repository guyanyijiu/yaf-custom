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
class AggregateCliFileHandler extends StreamHandler {

    public function handle(array $record) {

        if ( !$this->isHandling($record)) {
            return false;
        }

        // 计算运行时间
        $dur = number_format(microtime(true) - YAF_START, 6);
        $info = getrusage();
        // | 运行时间 | 当前内存 | 最大内存 | cpu utime | cpu stime
        $extra = [
            (float) $dur,
            memory_get_usage(),
            memory_get_peak_usage(),
            $info['ru_utime.tv_sec'] + $info['ru_utime.tv_usec'] / 1000000,
            $info['ru_stime.tv_sec'] + $info['ru_stime.tv_usec'] / 1000000,
        ];

        $record = $this->processRecord($record);

        $record['extra'] = $extra;

        $record['formatted'] = $this->getFormatter()->format($record);

        $this->write($record);

        return false === $this->bubble;
    }

}