<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();
$outputController = new OutputController();

$requester = $outputController->sendAuthkeyRequest($_GET['authkey']);
$result = $outputController->getTicketsFromRequester($requester);

echo json_encode($result);
