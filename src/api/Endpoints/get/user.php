<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();

if ($_GET) {
    $authkey = $_GET['authkey'];

    $dbResult = $databaseController->read();

} else {
    $result['result'] = 'No authkey given!';
}

echo json_encode($result);

$result = $databaseController->execCustomSqlQuery('SELECT * FROM user');

echo json_encode($result);