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
class AggregateFileHandler extends StreamHandler {

    public function handleBatch(array $records){

        // 计算运行时间
        $dur = number_format(microtime(true) - YAF_START, 6);

        // 获取YAF请求对象
        $request = \Yaf_Dispatcher::getInstance()->getRequest();

        // 增加一行自定义日志 唯一请求ID | 请求时间| 运行时间 | 客户端IP | 请求方法 | URI | 请求头
        $log = sprintf(
            \Uniqid::getRequestId() . "|%s|%s|%s|%s|%s|%s\n",
            date_create_from_format('U.u', sprintf('%.6F', YAF_START))->setTimezone(new \DateTimeZone(date_default_timezone_get()))->format('Y-m-d H:i:s.u'),
            $dur,
            $request->getServer('REMOTE_ADDR'),
            $request->getMethod(),
            $request->getRequestUri(),
            $request->getServer('HTTP_USER_AGENT')
        );

        // 格式化日志
        foreach ($records as $record) {
            if (!$this->isHandling($record)) {
                continue;
            }
            $record = $this->processRecord($record);
            $log .= $this->getFormatter()->format($record);
        }

        // 调用日志写入方法
        $this->write(['formatted' => $log]);
    }

}