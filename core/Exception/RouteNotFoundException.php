<?php

namespace EasyFrameworkCore\Exception;

use Exception;

class RouteNotFoundException extends Exception
{
    public function __construct($module, $action)
    {
        parent::__construct("Module `$module` and Action `$action` cannot be found");
    }
}