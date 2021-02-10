<?php

namespace Exception;

use Throwable;

class MiddlewareInterceptException extends \Exception
{
    private $content;

    public function __construct($content = "", $code = 0, Throwable $previous = null)
    {
        $this->content = $content;
        parent::__construct("", $code, $previous);
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
        return $this->content;
    }
}