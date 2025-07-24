<?php

namespace EasyFrameworkCore\Helper;

class Arr
{
    public static function wrap($val): array
    {
        if (is_array($val)) return $val;

        if (is_null($val)) return [];

        return [$val];
    }
}