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
        header('Access-Control-Allow-Origin:http://support-frontend.dom.olo');
        header('Access-Control-Allow-Methods:GET,PUT,POST,DELETE,PATCH,OPTIONS,Access-Control-Allow-Origin');
        header('Access-Control-Allow-Headers:Access-Control-Allow-Headers, Access-Control-Allow-Origin, Access-Control-Allow-Methods, Access-Control-Allow-Credentials, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers');
        header('Access-Control-Allow-Credentials:true');
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
