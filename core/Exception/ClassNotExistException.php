<?php

namespace EasyFrameworkCore\Exception;

use Exception;

class ClassNotExistException extends Exception
{
    public function __construct($className)
    {
        parent::__construct("Class `$className` does not exist");
    }
}