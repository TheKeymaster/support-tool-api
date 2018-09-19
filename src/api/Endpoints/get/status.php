<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();

$result = $databaseController->read('*', 'status');

echo json_encode($result);
