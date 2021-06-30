<?php

namespace Iras\LumenHprose\Server;

use Hprose\Filter;
use Illuminate\Support\Facades\Log;
//use NunoMaduro\Collision\ConsoleColor;
//use NunoMaduro\Collision\Highlighter;
use Iras\LumenHprose\Middleware\Contracts\AfterFilterHandler;
use Iras\LumenHprose\Middleware\Contracts\BeforeFilterHandler;
use Iras\LumenHprose\Middleware\Contracts\InvokeHandler;
use Iras\LumenHprose\Routing\Router;
use Laravel\Lumen\Application;
use RuntimeException;
use stdClass;

/**
 * Class ServerLaunch.
 */
class ServerLaunch
{
    /**
     * 提供的 server 类型.
     *
     * @var array
     */
    private const ALLOW_SERVER_TYPE = [
        'http',
        'tcp',
    ];

    /**
     * @param Application $app
     */
    public static function run(Application $app): void
    {
        self::registerServer($app);
        self::registerRouter($app);
        self::loadRoutes();
    }

    /**
     * 注册服务的单例.
     *
     * @param Application $app
     */
    private static function registerServer(Application $app): void
    {

        $app->singleton('hprose.server', function (Application $app) {
            $uri = config('hprose.uri');
            $rpcServerType = config('hprose.server');

            if (!in_array($rpcServerType, self::ALLOW_SERVER_TYPE, true)) {
                throw new RuntimeException('RPC_SERVER_TYPE 设置错误，只能为 HTTP 或者 TCP.');
            }
            if('tcp' == $rpcServerType){
                if (!class_exists(\Hprose\Swoole\Socket\Server::class)) {
                    throw new RuntimeException('未安装 hprose-swoole 包.');
                }
                $server = new \Hprose\Swoole\Socket\Server($uri);
            }else{
                throw new RuntimeException('暂不支持http.');
            }



            // 加载中间件
            $middlewareClasses = config('hprose.middleware');

            foreach ($middlewareClasses as $middlewareClass) {
                $middleware = new $middlewareClass();


                if ($middleware instanceof BeforeFilterHandler) {
                    $server->addBeforeFilterHandler($middleware);
                }

                if ($middleware instanceof Filter) {
                    $server->addFilter($middleware);
                }

                if ($middleware instanceof AfterFilterHandler) {
                    $server->addAfterFilterHandler($middleware);
                }

                if ($middleware instanceof InvokeHandler) {
                    $server->addInvokeHandler($middleware);
                }
            }

            // 是否开启 debug
            $server->debug = config('hprose.debug');

            // 错误处理
            $server->onSendError = function ($error, stdClass $context) {
                // 在 cli 上直接输出错误信息
                $highLighter = new Highlighter(new ConsoleColor());
                Log::channel('stderr')->error(PHP_EOL.$highLighter->highlight($error, 1));

                $message = json_encode(['message' => $error->getMessage(), 'code' => $error->getCode()]);
                throw new RuntimeException($message, $error->getCode());
            };

            return $server;
        });
    }

    /**
     * 注册路由的单例.
     *
     * @param Application $app
     */
    private static function registerRouter(Application $app): void
    {
        $app->singleton('hprose.router', function (Application $app) {
            return new Router();
        });
    }

    /**
     * 加载路由文件.
     */
    private static function loadRoutes(): void
    {
        $routeFilePath = base_path('routes/hprose.php');

        if (file_exists($routeFilePath)) {
            require $routeFilePath;
        }
    }
}
