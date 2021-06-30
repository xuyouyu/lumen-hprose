<?php
namespace Iras\LumenHprose;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
class HproseServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者加是否延迟加载.
     *
     * @var bool
     */
    protected $defer = true; // 延迟加载服务
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadCommands();
    }
    /**
     * Register the application services.
     *
     * @return void
     */
//    public function register()
//    {
//        if ('swoole' === config('hprose.server') && !extension_loaded('swoole')) {
//            Config::set('hprose.server', 'socket');
//        }
//    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function loadCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\HproseServer::class,
                Commands\HproseClientDemo::class,
            ]);
        }
    }
}

