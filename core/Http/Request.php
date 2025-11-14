<?php

namespace EasyFrameworkCore\Http;

class Request
{
    private array $_gets;

    private array $_posts;

    private array $attributes = [];

    public function __construct()
    {
        $this->_gets = $_GET;

        if (empty($_POST) && str_contains($_SERVER["CONTENT_TYPE"], "application/json")) {
            $this->_posts = json_decode(file_get_contents("php://input"), true);
        } else {
            $this->_posts = $_POST;
        }
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

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function isGetMethod(): bool
    {
        return $this->getMethod() === 'GET';
    }

    public function isPostMethod(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }
}