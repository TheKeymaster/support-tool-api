<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();

if (isset($_GET['authkey'])) {
    $authkey = $_GET['authkey'];

    $users = $databaseController->read('*', 'user', 1, "authkey = '$authkey'");

    if (array_key_exists(0, $users)) {
        $userId = $users[0]['id'];
        $result = $databaseController->execCustomSqlQuery("SELECT title, status FROM tickets WHERE tickets.createdby = '$userId'");
    } else {
        $result['result'] = false;
    }
} else {
    $result['result'] = false;
}

echo json_encode($result);
