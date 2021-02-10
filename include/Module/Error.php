<?php

namespace Module;

class Error extends \Module
{
    public function show404()
    {
        return \View::make('404');
    }
}