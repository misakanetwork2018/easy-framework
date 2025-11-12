<?php

namespace EasyFrameworkCore;

use EasyFrameworkCore\Exception\ClassNotExistException;
use EasyFrameworkCore\Http\Request;

class Render
{
    protected View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function __invoke(): void
    {
        include $this->view->getViewPath();
    }

    public function __get($name)
    {
        try {
            return $this->view->$name ?? App::make(Request::class)->$name;
        } catch (ClassNotExistException) {
            return null;
        }
    }

    public function __call($name, $arguments)
    {
        $this->view->$name(...$arguments);
    }
}