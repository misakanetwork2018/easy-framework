<?php

namespace EasyFrameworkCore\Exception;

use Exception;

class MissingModuleNamespaceConfigException extends Exception
{
    public function __construct($moduleNamespace)
    {
        parent::__construct("Namespace `$moduleNamespace` does not bound to the App");
    }
}