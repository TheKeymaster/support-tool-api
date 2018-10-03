<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setHeaders();

$databaseController = ApiOutputHelper::createDatabaseConnection();
$outputController = new OutputController();

// Set defaults for values that might be empty
$authkey = isset($_POST['authkey']) ? $_POST['authkey'] : false;
$title = isset($_POST['title']) ? $_POST['title'] : false;
$body = isset($_POST['body']) ? $_POST['body'] : false;
$isinternal =  isset($_POST['isinternal']) ? $_POST['isinternal'] : 0;

if ($authkey && $title && $body) {
    $requester = $databaseController->getUserByAuthkey($authkey)[0];
    $createdby = $requester['id'];

    $databaseController->create('tickets', ['createdby', 'title', 'status'], [$createdby, $title, 1]);

    $createdat = date('Y-m-d H:i:s');
    $ticketid = $databaseController->execCustomSqlQuery("SELECT * FROM tickets WHERE tickets.createdby = '$createdby' ORDER BY id DESC LIMIT 1")[0]['id'];
    $result = $outputController->createNewMessage($createdat, $createdby, $ticketid, $body, $isinternal);
    $result['result'] = true;
} else {
    $result['result'] = false;
}

echo json_encode($result);
