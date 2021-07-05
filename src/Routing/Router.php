<?php

namespace Iras\LumenHprose\Routing;

use Illuminate\Support\Arr;

/**
 * Class Router
 * @package Iras\LumenHprose\Routing
 */
class Router
{

    protected $groupStack = [];


    protected $methods = [];


    protected $prefix = '';



    /**
     * Desc：常见路由组
     * User: Administrator
     * Time: 2021/7/5 9:48
     * @param array $attributes
     * @param callable $callback
     */
    public function group(array $attributes, callable $callback): void
    {
        $attributes = $this->mergeLastGroupAttributes($attributes);

        if ((!isset($attributes['prefix']) || empty($attributes['prefix'])) && isset($this->prefix)) {
            $attributes['prefix'] = $this->prefix;
        }

        $this->groupStack[] = $attributes;

        $callback($this);

        array_pop($this->groupStack);
    }



    /**
     * Desc：添加路由方法
     * User: Administrator
     * Time: 2021/7/5 9:49
     * @param string $name
     * @param $action
     * @param array $options
     */
    public function add(string $name, $action, array $options = []): void
    {
        if (is_string($action)) {
            $action = ['controller' => $action, 'type' => 'method'];
        } elseif (is_callable($action)) {
            $action = ['callable' => $action, 'type' => 'callable'];
        }

        $action = $this->mergeLastGroupAttributes($action);

        if (!empty($action['prefix'])) {
            $name = ltrim(rtrim(trim($action['prefix'], '_').'_'.trim($name, '_'), '_'), '_');
        }

        switch ($action['type']) {
            case 'method':
                [$class, $method] = $this->parseController($action['namespace'], $action['controller']);

                $this->addMethod($method, $class, $name, $options);
                break;

            case 'callable':
                $this->addFunction($action['callable'], $name, $options);
                break;
        }
    }



    /**
     * Desc：获取所有的方法
     * User: Administrator
     * Time: 2021/7/5 9:49
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }


    /**
     * Desc：
     * User: Administrator
     * Time: 2021/7/5 9:49
     * @param array $attributes
     * @return array
     */
    protected function mergeLastGroupAttributes(array $attributes): array
    {
        if (empty($this->groupStack)) {
            return $this->mergeGroup($attributes, []);
        }

        return $this->mergeGroup($attributes, end($this->groupStack));
    }



    /**
     * Desc：
     * User: Administrator
     * Time: 2021/7/5 9:50
     * @param array $new
     * @param array $old
     * @return array
     */
    protected function mergeGroup(array $new, array $old): array
    {
        $new['namespace'] = $this->formatNamespace($new, $old);
        $new['prefix'] = $this->formatPrefix($new, $old);

        return array_merge_recursive(Arr::except($old, ['namespace', 'prefix']), $new);
    }



    /**
     * Desc：格式化命名空间
     * User: Administrator
     * Time: 2021/7/5 9:50
     * @param array $new
     * @param array $old
     * @return string|null
     */
    protected function formatNamespace(array $new, array $old): ?string
    {
        if (isset($new['namespace'], $old['namespace'])) {
            return trim($old['namespace'], '\\').'\\'.trim($new['namespace'], '\\');
        }
        if (isset($new['namespace'])) {
            return trim($new['namespace'], '\\');
        }

        return Arr::get($old, 'namespace');
    }



    /**
     * Desc：解析控制器.
     * User: Administrator
     * Time: 2021/7/5 9:50
     * @param $namespace
     * @param string $controller
     * @return array
     */
    protected function parseController($namespace, string $controller): array
    {
        [$classAsStr, $method] = explode('@', $controller);

        $class = app()->get(
            implode('\\', array_filter([$namespace, $classAsStr]))
        );

        return [$class, $method];
    }




    /**
     * Desc：格式化前缀
     * User: Administrator
     * Time: 2021/7/5 9:50
     * @param array $new
     * @param array $old
     * @return string
     */
    protected function formatPrefix(array $new, array $old): string
    {
        if (isset($new['prefix'])) {
            return trim(Arr::get($old, 'prefix'), '_').'_'.trim($new['prefix'], '_');
        }

        return Arr::get($old, 'prefix', '');
    }



    /**
     * Desc：添加匿名函数
     * User: Administrator
     * Time: 2021/7/5 9:50
     * @param callable $action
     * @param string $alias
     * @param array $options
     */
    private function addFunction(callable $action, string $alias, array $options): void
    {
        $this->methods[] = $alias;

        app('hprose.server')->addFunction($action, $alias, $options);
    }



    /**
     * Desc：添加方法
     * User: Administrator
     * Time: 2021/7/5 9:51
     * @param string $method
     * @param $class
     * @param string $alias
     * @param array $options
     */
    private function addMethod(string $method, $class, string $alias, array $options): void
    {
        $this->methods[] = $alias;

        app('hprose.server')->addMethod(
            $method,
            $class,
            $alias,
            $options
        );
    }
}
