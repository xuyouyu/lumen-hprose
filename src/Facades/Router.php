<?php

namespace Iras\LumenHprose\Facades;

use Illuminate\Support\Facades\Facade;



/**
 * Class Router.
 * @package Iras\LumenHprose\Facades
 * @method static void group(array $attributes, callable $callback)
 * @method static void add(string $name, $action, array $options = [])
 * @method static array getMethods()
 */
class Router extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'hprose.router';
    }
}
