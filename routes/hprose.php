<?php

/**
 * 路由设置
 */
use Iras\LumenHprose\Facades\Router;


//添加路由方法
Router::add('demo', 'Iras\LumenHprose\Controllers\DemoController@demo');
Router::add('test', 'Iras\LumenHprose\Controllers\DemoController@test');

//路由组
Router::group(['namespace' => 'Iras\LumenHprose\Controllers', 'prefix' => 'demo'], function ($route) {
    $route->add('test', 'TestController@test');
});
