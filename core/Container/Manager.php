<?php

namespace EasyFrameworkCore\Container;

use EasyFrameworkCore\App;
use EasyFrameworkCore\Exception\ClassNotExistException;

/**
 * 容器管理器
 * 仅支持单例模式
 * 不支持依赖注入（请直接调用Manager来获取单例）
 */
class Manager
{
    private array $instances = [];

    public function __construct(App $app)
    {
        $this->instances[App::class] = $app;
    }

    /**
     * 根据类名创建并获取实例
     *
     * @param $class
     * @param mixed ...$arguments
     * @return mixed
     * @throws ClassNotExistException
     */
    public function make($class, ...$arguments): mixed
    {
        if (!class_exists($class))
            throw new ClassNotExistException();

        if (!isset($this->instances[$class])) // 还没实例化就先实例化
            $this->instances[$class] = new $class(...$arguments);

        return $this->instances[$class];
    }
}