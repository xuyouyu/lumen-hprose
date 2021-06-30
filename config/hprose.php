<?php

return [


    //服务名称
    'service' => env('HPROSE_SERVICE'),

    //Debug 模式
    'debug' => env('HPROSE_DEBUG', false),

    //服务类型（http or tcp）
    'server' => env('HPROSE_SERVER', 'tcp'),

    //监听地址以及端口（tcp://0.0.0.0:8888  or http://localhost:8888）
    'uri' => env('HPROSE_URI', 'tcp://0.0.0.0:8888'),

    //中间件
    'middleware' => [
        Iras\LumenHprose\Middleware\ServerLoggerInvokeHandler::class,
    ],
];
