<?php

namespace api\Controllers;

class ConfigController
{
    const CONFIG_PATH = __DIR__ . '/../../../config.json';

    private $config;

    public function __construct()
    {
        $configAsJson = file_get_contents(self::CONFIG_PATH);
        $this->config = json_decode($configAsJson, true);
    }

    public function getConfig()
    {
        return $this->config;
    }
}