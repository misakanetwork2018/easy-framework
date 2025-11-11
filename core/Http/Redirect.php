<?php

namespace EasyFrameworkCore\Http;

use EasyFrameworkCore\App;

class Redirect extends Response
{
    public static function to($url): Response
    {
        return self::make($url);
    }

    public static function toAction($module = 'Index', $action = 'index', array $queries = []): Response
    {
        return self::make(App::generateUrl($module, $action));
    }

    public function render(): void
    {
        header("location: $this->content");
    }
}