<?php

namespace EasyFrameworkCore\Helper;

class Str
{
    public static function toPascal($uncamelized_words, $separator = '_'): string
    {
        return ucfirst(self::toCamel($uncamelized_words, $separator));
    }

    public static function toCamel($uncamelized_words, $separator = '_'): string
    {
        $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
    }

    public static function toUnderScore($camelCaps, $separator = '_'): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}