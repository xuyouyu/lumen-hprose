<?php

namespace Iras\LumenHprose\Controllers;

/**
 * 服务端demo
 */
class DemoController
{
    /**
     * @return string
     */
    public function getServiceName(): string
    {
        return config('hprose.service');
    }
}
