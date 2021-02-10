<?php

use Exception\MiddlewareInterceptException;
use Http\Request;

class Module
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var DB
     */
    protected $db;

    public function __construct()
    {
        $this->request = App::make(Request::class);
        $this->db = App::make(DB::class);
    }

    /**
     * 实现类似中间件的原理
     *
     * @param mixed ...$middleware
     * @throws \Exception\ClassNotExistException|MiddlewareInterceptException
     */
    public function middleware(...$middleware)
    {
        $request = App::make(Request::class);

        foreach ($middleware as $m) {
            $obj = new $m; /* @var \Middleware\Middleware $obj */
            $resp = $obj->handle($request, function () {
                return 'next';
            });

            if ($resp !== 'next') {
                throw new MiddlewareInterceptException($resp);
            }
        }
    }
}