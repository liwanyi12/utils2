<?php

namespace Liwanyi\Utils2;
use Workerman\Worker;
class Timer
{
    public function test(){
        $task = new Worker();
        // 开启多少个进程运行定时任务，注意业务是否在多进程有并发问题
        $task->count = 1;
        $task->onWorkerStart = function (Worker $task)
        {
            // 每2.5秒执行一次
            $time_interval = 2.5;
            Timer::add($time_interval, function () {
                echo "task run\n";
            });
        };
        Worker::runAll();
    }



}

















