<?php

return [
    //监听的IP
    'host' => '127.0.0.1',
    //端口
    'port' => 8888,

    //服务类型（tcp or http）
    'server_type'=>'tcp',

    'debug'=>true,

    //中间件
    'middleware' => [
        'log'=>Iras\LumenHprose\Middleware\ServerLoggerMiddleware::class,//调用日志记录中间件
    ],



    //swoole 启动设置，具体参考swoole文档
    'swoole_setting'=>[
        'daemonize' => 0,
        'dispatch_mode' => 2,
        'worker_num' => 4,
        'max_request' => 5000,
        'log_file' => storage_path('logs/swoole.log'),
        'log_level' => 5,
        'pid_file' => storage_path('logs/swoole.pid'),
        'open_tcp_nodelay' => 1,
    ],

];
