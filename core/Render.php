<?php

namespace EasyFrameworkCore;

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
        return $this->view->$name ?? null;
    }

    public function __call($name, $arguments)
    {
        $this->view->$name(...$arguments);
    }
}