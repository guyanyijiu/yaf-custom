<?php

require __DIR__ . '/../cli.php';

/**
 * 多进程任务处理类
 * 适用于各进程可以独立处理的任务，子进程之间互不通信互不影响
 * 主进程会在子进程退出之后重新启动新的子进程
 * 子进程会在主进程退出之后处理完当前任务之后自动退出
 *
 * Class SwooleMaster
 *
 * @author  liuchao
 */
class SwooleMaster {

    /**
     * 主进程 pid
     *
     * @var int
     */
    protected $master_pid = 0;

    /**
     * 所有子进程的 pid
     *
     * @var array
     */
    protected $workers = [];

    /**
     * 同时开启的子进程数
     *
     * @var int
     */
    protected $worker_count = 1;

    /**
     * 重启异常退出子进程的最大次数
     *
     * @var int
     */
    protected $max_reboot = 100;

    /**
     * 每个子进程处理的最大任务数，处理完之后自动退出，可以防止内存泄漏
     *
     * @var int
     */
    protected $max_worker_task = 1000000;

    /**
     * 进程名字，macOS 不支持设置进程名
     *
     * @var string
     */
    protected $proccess_name = 'default';

    /**
     * SwooleMaster constructor.
     */
    public function __construct() {
        try {
            \Log::info('测试');
            swoole_process::daemon();
            swoole_set_process_name('php ' . $this->proccess_name . ' : master');
            $this->master_pid = posix_getpid();

            $this->run();
            $this->processWait();
        } catch (\Exception $e) {
            \Log::error($this->proccess_name . '进程启动异常:' . $e->getMessage());
            exit;
        }
    }

    /**
     * 启动子进程
     *
     *
     * @author  liuchao
     */
    public function run() {
        for ($i = 0; $i < $this->worker_count; $i++) {
            $this->startWorker($i);
        }
    }

    /**
     * 启动子进程并开始执行任务
     *
     * @param $index
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function startWorker($index) {
        $process = new swoole_process(function (swoole_process $worker) use ($index) {
            swoole_set_process_name('php ' . $this->proccess_name . ' : worker-' . $index);
            $j = $this->max_worker_task;
            while ($j > 0) {
                $this->checkMaster($worker);

                $res = false;
                try {
                    $res = $this->task();
                } catch (\Throwable $e) {
                    \Log::error('任务执行异常:' . $e->getMessage());
                }
                if ( !$res) {
                    sleep(3);
                    continue;
                }
                $j--;
            }

        }, false, false);

        $pid = $process->start();
        $this->workers[$index] = $pid;

        return $pid;
    }

    /**
     * 检查父进程
     *
     * @param $worker
     *
     * @author  liuchao
     */
    public function checkMaster(&$worker) {
        if ( !swoole_process::kill($this->master_pid, 0)) {
            \Log::error($this->proccess_name . " 父进程退出, 子进程 [{$worker->pid}] 也退出");
            $worker->exit();
        }
    }

    /**
     * 重启子进程
     *
     * @param $ret
     *
     * @author  liuchao
     */
    public function rebootWorker($ret) {
        $pid = $ret['pid'];
        $code = $ret['code'];
        if ($code !== 0) {
            $this->max_reboot--;
        }
        \Log::info('子进程退出: code=' . $code);

        if ($this->max_reboot < 0) {
            \Log::error('已达到子进程异常退出重启最大次数');
            exit;
        }
        $index = array_search($pid, $this->workers);
        if ($index !== false) {
            $index = intval($index);
            $new_pid = $this->startWorker($index);
            \Log::info("重启子进程: {$index}={$new_pid} 完成");

            return;
        }
        \Log::error('重启子进程错误: no pid');
    }

    /**
     * 监控子进程退出
     *
     *
     * @author  liuchao
     */
    public function processWait() {
        while (1) {
            if (count($this->workers)) {
                $ret = swoole_process::wait();
                if ($ret) {
                    $this->rebootWorker($ret);
                }
            } else {
                break;
            }
        }
    }

    /**
     * 子进程的任务
     * 如果该任务返回 true 则认为任务执行成功一次，并立即进行下一次执行
     * 如果返回 false 则认为任务执行失败一次， sleep(3) 之后再进行下一次执行
     *
     * @return bool
     *
     * @author  liuchao
     */
    public function task() {
        return true;
    }

}
