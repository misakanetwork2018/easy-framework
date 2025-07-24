<?php

namespace EasyFrameworkCore\Http;

class Redirect extends Response
{
    public static function to($url): Response
    {
        return self::make($url);
    }

    public function render(): void
    {
        header("location: $this->content");
    }
}