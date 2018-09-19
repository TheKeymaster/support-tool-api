<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();

if ($_GET) {
    $authkey = $_GET['authkey'];

    $result = $databaseController->read('*', 'user', 1000, "authkey = '$authkey'");
} else {
    $result['result'] = 'No authkey given!';
}

echo json_encode($result);
