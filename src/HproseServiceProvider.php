<?php
namespace Iras\LumenHprose;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class HproseServiceProvider extends ServiceProvider
{

    protected $defer = true; // 延迟加载服务


    public function boot()
    {

        $this->commands([
            Commands\HproseServer::class,
        ]);

    }

    public function register()
    {

    }

}

