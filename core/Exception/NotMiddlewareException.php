<?php

namespace EasyFrameworkCore\Exception;

use Exception;

class NotMiddlewareException extends Exception
{
    public function __construct(string $class_name)
    {
        parent::__construct("Class $class_name is not a middleware");
    }
}