<?php

namespace EasyFrameworkCore;

use EasyFrameworkCore\Exception\MiddlewareInterceptException;
use EasyFrameworkCore\Http\Request;

class Module
{
    protected Request $request;

    protected DB $db;

    /**
     * @throws \EasyFrameworkCore\Exception\ClassNotExistException
     */
    public function __construct()
    {
        $this->request = App::make(Request::class);
        $this->db = App::make(DB::class);
    }

    /**
     * 实现类似中间件的原理
     *
     * @param mixed ...$middleware
     * @throws \EasyFrameworkCore\Exception\ClassNotExistException|MiddlewareInterceptException
     */
    public function middleware(...$middleware): void
    {
        $request = App::make(Request::class);

        foreach ($middleware as $m) {
            $obj = new $m; /* @var \EasyFrameworkCore\Middleware\Middleware $obj */
            $resp = $obj->handle($request, function () {
                return 'next';
            });

            if ($resp !== 'next') {
                throw new MiddlewareInterceptException($resp);
            }
        }
    }
}