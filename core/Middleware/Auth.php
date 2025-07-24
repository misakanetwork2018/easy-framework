<?php

namespace EasyFrameworkCore\Middleware;

use EasyFrameworkCore\Http\Request;

class Auth implements Middleware
{
    public function handle(Request $request, $next)
    {
        // 这里做一些认证的东西

        return $next($request);
    }
}