<?php

namespace Iras\LumenHprose\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Iras\LumenHprose\Middleware\InvokeHandler;
use stdClass;

/**
 * Class ServerLoggerInvokeHandler.
 */
final class TestMiddleware extends InvokeHandler
{
    /**
     * Desc：测试中间件
     * User: Administrator
     * Time: 2021/7/5 9:47
     * @param $name
     * @param array $args
     * @param stdClass $context
     * @param Closure $next
     * @return mixed
     */
    public function handle($name, array &$args, stdClass $context, Closure $next)
    {
        if($name == 'demo_test'){
            var_dump('test');
        }
        $result = $next($name, $args, $context, $next);

        return $result;
    }
}
