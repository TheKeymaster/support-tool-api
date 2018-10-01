<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();
$outputController = new OutputController();

// Set defaults for values that might be empty
$authkey = isset($_POST['authkey']) ? $_POST['authkey'] : false;
$ticketid = isset($_POST['ticketid']) ? $_POST['ticketid'] : false;
$body = isset($_POST['body']) ? $_POST['body'] : false;
$isinternal =  isset($_POST['isinternal']) ? $_POST['isinternal'] : 0;

if ($authkey && $ticketid && $body) {
    $requester = $databaseController->getUserByAuthkey($authkey)[0];
    $createdby = $requester['id'];
    $createdat = date('Y-m-d H:i:s');

    $result = $outputController->createNewMessage($createdat, $createdby, $ticketid, $body, $isinternal);
    $result['result'] = true;
} else {
    $result['result'] = false;
}

echo json_encode($result);
