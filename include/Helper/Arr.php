<?php

namespace Helper;

class Arr
{
    public static function wrap($val)
    {
        if (is_array($val)) return $val;

        if (is_null($val)) return [];

        return [$val];
    }
}