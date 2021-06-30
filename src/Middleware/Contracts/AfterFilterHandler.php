<?php

declare(strict_types=1);

namespace Iras\LumenHprose\Middleware\Contracts;

use Closure;
use stdClass;


abstract class AfterFilterHandler
{
    /**
     * @param mixed    $request
     * @param stdClass $context
     * @param Closure  $next
     *
     * @return mixed
     */
    public function __invoke($request, stdClass $context, Closure $next)
    {
        return $this->handle($request, $context, $next);
    }

    abstract public function handle($request, stdClass $context, Closure $next);
}
