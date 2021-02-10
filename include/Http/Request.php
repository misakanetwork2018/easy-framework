<?php

namespace Http;

class Request
{
    /**
     * @var array
     */
    private $_gets;

    /**
     * @var array
     */
    private $_posts;

    /**
     * @var array
     */
    private $attributes = [];

    public function __construct()
    {
        $this->_gets = $_GET;

        $this->_posts = $_POST;
    }

    public function get($key = null, $default = null)
    {
        if (is_null($key))
            return $this->_gets;

        return $this->_gets[$key] ?? $default;
    }

    public function post($key = null, $default = null)
    {
        if (is_null($key))
            return $this->_posts;

        return $this->_posts[$key] ?? $default;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }
}