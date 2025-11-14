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
            if ($this->view->hasData($name))
                return $this->view->getData($name);
            return App::make(Request::class)->$name;
        } catch (ClassNotExistException) {
            return null;
        }
    }

    public function __call($name, $arguments)
    {
        echo $this->view->getData($name);
    }

    public function __isset($name)
    {
        try {
            if ($this->view->hasData($name))
                return true;

            if (isset(App::make(Request::class)->$name))
                return true;
        } catch (ClassNotExistException) {}
        return false;
    }
}