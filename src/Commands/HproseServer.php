<?php

namespace Iras\LumenHprose\Commands;

//use Hprose\Filter;
use Iras\LumenHprose\Facades\Router;
//use Iras\LumenHprose\Middleware\Contracts\AfterFilterHandler;
//use Iras\LumenHprose\Middleware\Contracts\BeforeFilterHandler;
//use Iras\LumenHprose\Middleware\Contracts\InvokeHandler;
//use Iras\LumenHprose\Server\ServerLaunch;
use Illuminate\Console\Command;
use Laravel\Lumen\Application;
use RuntimeException;

/**
 * 启动服务
 */
class HproseServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hprose:server {action : how to handle the tcp server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle swoole tcp server with start | restart | reload | stop | status';


    private const ALLOW_SERVER_TYPE = [
        'http',
        'tcp',
    ];


    /**
     * Execute the console command.
     *
     * @param Application $app
     *
     * @return mixed
     */
    public function handle(Application $app)
    {
        if (!extension_loaded('swoole')) {
            throw new RuntimeException('请安装swoole扩展');
        }


        $action = $this->argument('action');
        switch ($action) {
            case 'start':
                $this->start($app);
                break;
            case 'restart':
                $this->restart($app);
                break;
            case 'reload':
                $this->reload();
                break;
            case 'stop':
                $this->stop();
                break;
            case 'status':
                $this->status();
                break;
            default:
                $this->error('Please type correct action . start | restart | stop | reload | status');
        }

    }





    protected function start(Application $app)
    {
        if ($this->getPid()) {
            $this->error('swoole server is already running');
            exit(1);
        }

        \Iras\LumenHprose\Server\HproseServer::run($app);


        $this->outputInfo();
        $this->info('starting swoole server...');
        $server = app('hprose.server');

        $swooleSetting = config('hprose.swoole_setting');
        $server->set($swooleSetting);

        $server->start();
    }

    protected function restart(Application $app)
    {
        $this->info('stopping swoole server...');
        $pid = $this->sendSignal(SIGTERM);
        $time = 0;
        while (posix_getpgid($pid)) {
            sleep(1);
            $time++;
            if ($time > 10) {
                $this->error('timeout...');
                exit(1);
            }
        }
        $this->info('done');
        $this->start($app);
    }

    protected function reload()
    {
        $this->info('reloading...');
        $this->sendSignal(SIGUSR1);
        $this->info('done');
    }

    protected function stop()
    {
        $this->info('immediately stopping...');
        $this->sendSignal(SIGTERM);
        $this->info('done');
    }

    protected function status()
    {
        $pid = $this->getPid();
        if ($pid) {
            $this->info('swoole server is running. master pid : ' . $pid);
        } else {
            $this->error('swoole server is not running!');
        }
    }

    protected function sendSignal($sig)
    {
        $pid = $this->getPid();
        if ($pid) {
            posix_kill($pid, $sig);
        } else {
            $this->error('swoole is not running!');
            exit(1);
        }
        return $pid;
    }

    protected function getPid()
    {
        $pid_file = config('hprose.swoole_setting')['pid_file'];
        if (file_exists($pid_file)) {
            $pid = intval(file_get_contents($pid_file));
            if (posix_getpgid($pid)) {
                return $pid;
            } else {
                unlink($pid_file);
            }
        }
        return false;
    }




    /**
     * 输出基础信息.
     */
    protected function outputInfo(): void
    {
//        $this->comment('Service:');
//        $this->output->writeln(sprintf(' - <info>%s</info>', config('hprose.service')));
//        $this->output->newLine();

        $this->comment('版本:');
        $this->output->writeln(sprintf(' - Laravel/Lumen=<info>%s</info>', app()->version()));
        $this->output->writeln(sprintf(' - Hprose-php=<info>2.0.*</info>'));
        $this->output->newLine();

        $this->comment('启动的服务器类型:');
        $this->line(sprintf(' - <info>%s</info>', config('hprose.server_type')));
        $this->output->newLine();

        $this->comment('监听:');
        $this->line(sprintf(' - <info>%s</info>', config('hprose.host') . ':' . config('hprose.port')));
        $this->output->newLine();

        $this->comment('加载的中间件:');
        $middlewareClasses = config('hprose.middleware');
        foreach ($middlewareClasses as $middlewareClass) {
            $this->line(sprintf(' - <info>%s</info>', $middlewareClass));
        }
        $this->output->newLine();

        $this->comment('可调用远程方法:');
        $methods = Router::getMethods();
        if ($methods) {
            foreach ($methods as $method) {
                $this->line(sprintf(' - <info>%s</info>', $method));
            }
            $this->output->newLine();
        } else {
            $this->line(sprintf(' - <info>无可调用方法</info>'));
        }
    }
}
