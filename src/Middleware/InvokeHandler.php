<?php

namespace Iras\LumenHprose\Middleware;

use Closure;
use stdClass;


abstract class InvokeHandler
{


    /**
     * Desc：全局中间件 （还有其他中间件，该扩展包目前没有实现，现实中用这一个就能满足需求，可以参考文档：https://github.com/hprose/hprose-php/wiki/12-Hprose-%E4%B8%AD%E9%97%B4%E4%BB%B6）
     * User: Administrator
     * Time: 2021/7/5 9:42
     * @param $name
     * @param array $args
     * @param stdClass $context
     * @param Closure $next
     * @return mixed
     */
    public function __invoke($name, array &$args, stdClass $context, Closure $next)
    {
        return $this->handle($name, $args, $context, $next);
    }

    abstract public function handle($name, array &$args, stdClass $context, Closure $next);
}
