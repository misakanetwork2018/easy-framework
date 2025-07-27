<?php

namespace EasyFrameworkCore\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
class Middleware
{
    /**
     * 中间件类
     */
    public readonly string $className;

    public function __construct(string $class_name)
    {
        $this->className = $class_name;
    }
}