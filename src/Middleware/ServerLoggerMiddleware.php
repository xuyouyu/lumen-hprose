<?php

namespace Iras\LumenHprose\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Iras\LumenHprose\Middleware\InvokeHandler;
use stdClass;

/**
 * Class ServerLoggerInvokeHandler
 * @package Iras\LumenHprose\Middleware
 */
final class ServerLoggerMiddleware extends InvokeHandler
{


    /**
     * Desc： 服务端的调用日志记录.
     * User: Administrator
     * Time: 2021/7/5 9:46
     * @param $name
     * @param array $args
     * @param stdClass $context
     * @param Closure $next
     * @return mixed
     */
    public function handle($name, array &$args, stdClass $context, Closure $next)
    {
        $server = app('hprose.server');

        $beginTime = microtime(true);
        if ($server->debug) {
            Log::channel('stderr')->debug(
                sprintf(
                    '[%s] (%s) 调用开始, 传入参数: %s.',
                    config('hprose.service'),
                    $name,
                    json_encode($args)
                )
            );
        }

        $result = $next($name, $args, $context, $next);

        $endTime = microtime(true);
        if ($server->debug) {
            Log::channel('stderr')->debug(
                sprintf(
                    '[%s] (%s) 调用结束, 耗时: %s.',
                    config('hprose.service'),
                    $name,
                    round($endTime - $beginTime, 6)
                )
            );
        }

        return $result;
    }
}
