<?php

namespace EasyFrameworkCore\Helper;

/**
 * 一次性数据，阅后即焚
 */
class Flash
{
    public static function setData($key, $value): void
    {
        $_SESSION["FLASH_" . $key] = $value;
    }

    public static function getData($key)
    {
        $value = $_SESSION["FLASH_" . $key] ?? null;
        unset($_SESSION["FLASH_" . $key]);
        return $value;
    }
}