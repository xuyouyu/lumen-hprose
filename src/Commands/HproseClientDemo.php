<?php

namespace Iras\LumenHprose\Commands;

use Hprose\Socket\Client;
use Illuminate\Console\Command;
use Throwable;


class HproseClientDemo extends Command
{

    protected $signature = 'hprose:client demo';

    protected $description = 'Hprose heartbeat';


    public function handle()
    {
        //调用单个方法
        $client = new \Hprose\Socket\Client('tcp://127.0.0.1:8888', false);
        echo $client->test();


        //调用路由组方法（demo未路由组前缀）
        echo $client->demo->test();
    }
}
