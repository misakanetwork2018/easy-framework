<?php

namespace EasyFrameworkCore;

use EasyFrameworkCore\Http\Request;

interface Middleware
{
    public function handle(Request $request, $next);
}