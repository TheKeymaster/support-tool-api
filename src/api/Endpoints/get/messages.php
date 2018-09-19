<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();

if (isset($_GET['authkey']) && isset($_GET['ticketid'])) {
    $authkey = $_GET['authkey'];
    $ticketid = $_GET['ticketid'];

    $users = $databaseController->read('*', 'user', 1, "authkey = '$authkey'");

    if (array_key_exists(0, $users)) {
        $userId = $users[0]['id'];
        $result = $databaseController->execCustomSqlQuery("SELECT * FROM tickets LEFT JOIN messages m on tickets.id = m.ticketid WHERE tickets.createdby = '$userId' AND tickets.id = '$ticketid'");
    } else {
        $result['result'] = false;
    }
} else {
    $result['result'] = false;
}

echo json_encode($result);
