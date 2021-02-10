<?php

namespace Middleware;

use Http\Request;

interface Middleware
{
    public function handle(Request $request, $next);
}