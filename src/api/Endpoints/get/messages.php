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
        if ($users[0]['role'] <= 1) {
            $result = $databaseController->execCustomSqlQuery("SELECT tickets.title, tickets.status, m.createdat, m.createdby, m.body, m.isinternal FROM tickets LEFT JOIN messages m ON tickets.id = m.ticketid WHERE tickets.createdby = '$userId' AND tickets.id = '$ticketid' AND isinternal = 0");
        } else {
            $result = $databaseController->execCustomSqlQuery("SELECT tickets.title, tickets.status, m.createdat, m.createdby, m.body, m.isinternal FROM tickets LEFT JOIN messages m ON tickets.id = m.ticketid WHERE tickets.id = '$ticketid'");
        }
    } else {
        $result['error'] = 'Invalid authkey given!';
    }
} else {
    $result['error'] = 'No authkey and/or ticketid given!';
}

echo json_encode($result);
