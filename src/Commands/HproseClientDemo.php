<?php

namespace Iras\LumenHprose\Commands;

use Hprose\Socket\Client;
use Illuminate\Console\Command;
use Throwable;

/**
 * 测试案例
 */
class HproseClientDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hprose:client:demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hprose heartbeat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new \Hprose\Socket\Client('tcp://127.0.0.1:8888', false);
        echo $client->getServiceName();
    }
}
