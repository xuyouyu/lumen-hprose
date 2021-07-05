# Lumen-hprose

## 安装

```shell
composer require iras/lumen-hprose
```

## 配置
>1、在 bootstrap/app.php 注册 服务组件
>
>>```shell
>>  $app->register(\Iras\LumenHprose\ServiceProvider::class);
>>  ```
>     
>2、复制config目录下的hprose.php到框架的config目录，根据注释修改配置
>
>
>3、复制routes目录下的hprose.php到框架的routes目录


## 使用
>添加路由方法
>>```shell
>> use Iras\LumenHprose\Facades\Router;   
>>
>> Router::add(string $name, string|callable $action, array $options = []);
>> ```
>*  $name  string  方法名称（远程调用该名称）
>*  $action  string|callable  例：Iras\LumenHprose\Controllers\DemoController@demo
>*  $options  array  特殊设置
>
>发布远程调用方法 demo
>>```shell
>> use Iras\LumenHprose\Facades\Router;   
>>
>> Router::add('demo', 'Iras\LumenHprose\Controllers\DemoController@demo');
>> ```
>
>
>具体的服务逻辑
>>```shell
>><?php
>>  
>>  namespace Iras\LumenHprose\Controllers;
>>  
>>  /**
>>  * Class DemoController.
>>   */
>>  class DemoController
>>  {
>>      /**
>>       * @return string
>>       */
>>      public function demo(): string
>>      {
>>          return "demo";
>>      }
>>  }
>> ```

>客户端调用
>>```shell
>>$client = new \Hprose\Socket\Client('tcp://127.0.0.1:8888', false);
>>
>>echo $client->demo();
>>```
>>
>
>路由组添加参考hprose.php中的路由组添加（可添加路由前缀，和炉门框架路由组一样）
>客户端调用
>>```shell
>>echo $client->demo->test();//有路由前缀
>>
>>echo $client->test();//无前缀
>>```
>
>中间件（该包只实现了InvokeHandler 中间件，InvokeHandler中间件基本满足平常开发需要），更多中间件功能可以参考文档：https://github.com/hprose/hprose-php/wiki/12-Hprose-%E4%B8%AD%E9%97%B4%E4%BB%B6
>>
>>中间件需要继承middleware下的InvokeHandler类
>>```shell
>>use Iras\LumenHprose\Middleware\InvokeHandler;
>>  use stdClass;
>>  
>>  /**
>>   * Class ServerLoggerInvokeHandler.
>>   */
>>  final class TestMiddleware extends InvokeHandler
>>  {
>>      /**
>>       * Desc：测试中间件
>>       * User: Administrator
>>       * Time: 2021/7/5 9:47
>>       * @param $name
>>       * @param array $args
>>       * @param stdClass $context
>>       * @param Closure $next
>>       * @return mixed
>>       */
>>      public function handle($name, array &$args, stdClass $context, Closure $next)
>>      {
>>          if($name == 'demo_test'){
>>              var_dump('test');
>>          }
>>          $result = $next($name, $args, $context, $next);
>>  
>>          return $result;
>>      }
>>  }
>>```
>>
>>中间件的加载顺序决定了执行顺序，注意config/hprose.php中的middleware数组元素的顺序
>
## 服务启动、停止、重启
```shell script
php artisan hprose:server start  //启动

php artisan hprose:server restart //重启（平滑重启）  

php artisan hprose:server stop  //停止
```

## 调试
```shell script
php artisan hprose:client demo
```