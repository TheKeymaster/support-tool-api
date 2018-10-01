<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$outputController = new OutputController();

$_GET['authkey'] = isset($_GET['authkey']) ? $_GET['authkey'] : null;
$_GET['ticketid'] = isset($_GET['ticketid']) ? $_GET['ticketid'] : null;

$requester = $outputController->sendAuthkeyRequest($_GET['authkey']);
$result = $outputController->getMessagesFromRequesterAndTicketId($requester, $_GET['ticketid']);

echo json_encode($result);
