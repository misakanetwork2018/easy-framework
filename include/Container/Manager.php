<?php

namespace Container;

use App;
use Exception\ClassNotExistException;

/**
 * 容器管理器
 * 仅支持单例模式
 * 不支持依赖注入（请直接调用Manager来获取单例）
 */
class Manager
{
    /**
     * @var array
     */
    private $instances;

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
    public function make($class, ...$arguments)
    {
        if (!class_exists($class))
            throw new ClassNotExistException();

        if (!isset($this->instances[$class])) // 还没实例化就先实例化
            $this->instances[$class] = new $class(...$arguments);

        return $this->instances[$class];
    }
}