<?php

namespace api\Helpers;

use api\Controllers\ConfigController;
use api\Controllers\DatabaseController;

class ApiOutputHelper
{
    /**
     * Sets the default header to be json.
     */
    public static function setJsonHeader()
    {
        header('Content-Type:application/json;charset=utf8');
        header('Access-Control-Allow-Origin: *');
    }

    /**
     * Returns a new DatabaseController based on the config.
     *
     * @return DatabaseController
     */
    public static function createDatabaseConnection()
    {
        $configController = new ConfigController();
        $config = $configController->getConfig();

        return new DatabaseController($config['host'], $config['username'], $config['password']);
    }
}
