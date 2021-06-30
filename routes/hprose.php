<?php

//rpc 服务路由

use Iras\LumenHprose\Facades\Router;

Router::add('getServiceName', 'Iras\LumenHprose\Controllers\DemoController@getServiceName');
