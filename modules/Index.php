<?php

namespace App;

use EasyFrameworkCore\Module;

class Index extends Module
{
    public function index(): string
    {
        return "Hello";
    }
}