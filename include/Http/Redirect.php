<?php

namespace Http;

class Redirect extends Response
{
    public static function to($url)
    {
        return self::make($url);
    }

    public function render()
    {
        header("location: {$this->getContent()}");
    }
}