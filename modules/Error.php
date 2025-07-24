<?php

namespace App;

use EasyFrameworkCore\Module;
use EasyFrameworkCore\View;

class Error extends Module
{
    /**
     * @throws \EasyFrameworkCore\Exception\ClassNotExistException
     */
    public function show404(): View
    {
        return View::make('404');
    }
}