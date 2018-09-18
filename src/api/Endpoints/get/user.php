<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\ConfigController;
use api\Controllers\DatabaseController;

header('Content-Type:application/json;charset=utf8');

$configController = new ConfigController();
$config = $configController->getConfig();

$databaseController = new DatabaseController($config['host'], $config['username'], $config['password']);

$result = $databaseController->execCustomSqlQuery('SELECT * FROM user');

echo json_encode($result);