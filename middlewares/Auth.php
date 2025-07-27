<?php

namespace Middlewares;

use EasyFrameworkCore\Http\Request;
use EasyFrameworkCore\Middleware;

class Auth implements Middleware
{
    public function handle(Request $request, $next)
    {
        return $next($request);
    }
}