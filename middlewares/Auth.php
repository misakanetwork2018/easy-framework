<?php

namespace Middlewares;

use EasyFrameworkCore\Http\Request;
use EasyFrameworkCore\Middleware;

class Auth implements Middleware
{
    public function handle(Request $request)
    {
        // 在此实现中间件，若有return则中断请求并马上显示返回值
    }
}