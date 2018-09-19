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

        if ($users[0]['role'] <= 1) {
            $result = $databaseController->execCustomSqlQuery("SELECT title, status FROM tickets WHERE tickets.createdby = '$userId'");
        } else {
            $result = $databaseController->execCustomSqlQuery("SELECT title, status FROM tickets");
        }
    } else {
        $result['error'] = 'Invalid authkey given!';
    }
} else {
    $result['error'] = 'No authkey given!';
}

echo json_encode($result);
