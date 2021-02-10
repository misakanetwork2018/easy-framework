<?php

class Config
{
    /**
     * @var array
     */
    private $configs = [];

    public function __construct()
    {
        if (file_exists($file = APP_ROOT . "/config.php"))
        $this->configs = include_once $file;
    }

    public function get($key, $default = null)
    {
        $parts = explode(".", $key);

        $newData = $this->configs;

        foreach ($parts as $part) {
            if (is_array($newData) && array_key_exists($part, $newData)) {
                $newData = $newData[$part];
            } else {
                $newData = $default;
                break;
            }
        }

        return $newData;
    }

    public function set($key, $val)
    {
        $this->configs[$key] = $val;
    }

    public function show($key, $default = null)
    {
        echo $this->get($key, $default);
    }
}