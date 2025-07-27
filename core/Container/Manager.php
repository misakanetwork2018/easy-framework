<?php

namespace EasyFrameworkCore\Container;

use EasyFrameworkCore\App;
use EasyFrameworkCore\Attribute\Inject;
use EasyFrameworkCore\Exception\ClassNotExistException;
use ReflectionObject;

/**
 * 容器管理器
 * 仅支持单例模式
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
     * 当前版本支持依赖注入，目前只支持通过属性注入(必须有Inject注解)
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

        // 依赖注入
        $this->injectProc($obj = $this->instances[$class]);

        return $obj;
    }

    public function injectProc($obj): void
    {
        $reflection = new ReflectionObject($obj);

        foreach ($reflection->getProperties() as $property) {
            if (count($property->getAttributes(Inject::class)) > 0) {
                // 需要注入
                $type = $property->getType();
                if ($type !== null && !$type->isBuiltin()) {
                    try {
                        $property->setValue($obj, $this->make($type->getName()));
                    } catch (ClassNotExistException) {
                        // 注入失败不处理
                    }
                }
            }
        }
    }
}