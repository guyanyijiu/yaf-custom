<?php

require __DIR__ . '/../cli.php';

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
    protected $max_reboot = 10;

    /**
     * 每个子进程处理的最大任务数，处理完之后自动退出，可以防止内存泄漏
     *
     * @var int
     */
    protected $max_worker_task = 100;

    protected $proccess_name = 'default';

    /**
     * SwooleMaster constructor.
     */
    public function __construct() {
        try {
            swoole_process::daemon();
            swoole_set_process_name('php beanstalkd consumer ' . $this->proccess_name . ' : master');
            $this->master_pid = posix_getpid();
            $this->run();
            $this->processWait();
        } catch (\Exception $e) {
            Log::error('swoole 进程异常:' . $e->getMessage());
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
     * 启动子进程，创建任务
     *
     * @param $index
     *
     * @return mixed
     *
     * @author  liuchao
     */
    public function startWorker($index) {
        $process = new swoole_process(function (swoole_process $worker) use ($index) {
            swoole_set_process_name('php beanstalkd consumer ' . $this->proccess_name . ' : worker-' . $index);
            for ($j = 0; $j < $this->max_worker_task; $j++) {
                $this->checkMaster($worker);
                $res = false;
                try{
                    $res = $this->task();
                }catch (\Exception $e){
                    Log::error('任务执行异常:' . $e->getMessage());
                }
                if ( !$res) {
                    $j--;
                    sleep(3);
                    continue;
                }
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
            Log::error("Master process exited, I [{$worker->pid}] also quit");
            $worker->exit();
        }
    }

    /**
     * 重启子进程
     *
     * @param $ret
     *
     * @throws Exception
     *
     * @author  liuchao
     */
    public function rebootWorker($ret) {
        $pid = $ret['pid'];
        $code = $ret['code'];
        if($code !== 0){
            $this->max_reboot --;
        }
        Log::info('退出码:' . $code);
        Log::info('max_reboot:' . $this->max_reboot);
        if($this->max_reboot < 0 ){
            Log::error('已达到异常退出重启最大次数');
            exit;
        }
        $index = array_search($pid, $this->workers);
        if ($index !== false) {
            $index = intval($index);
            $new_pid = $this->startWorker($index);
            Log::info("rebootWorker: {$index}={$new_pid} Done\n");
            return;
        }
        Log::error('rebootWorker Error: no pid');
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
     *
     * @return bool
     *
     * @author  liuchao
     */
    public function task() {
        return true;
    }

}
