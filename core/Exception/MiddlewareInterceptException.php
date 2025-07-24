<?php

namespace EasyFrameworkCore\Exception;

use Exception;
use Throwable;

class MiddlewareInterceptException extends Exception
{
    public readonly string $content;

    public function __construct($content = "", $code = 0, ?Throwable $previous = null)
    {
        $this->content = $content;
        parent::__construct("", $code, $previous);
    }
}