<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();
$outputController = new OutputController();

$_GET['authkey'] = isset($_GET['authkey']) ? $_GET['authkey'] : null;

$requester = $outputController->sendAuthkeyRequest($_GET['authkey']);
$result = $outputController->getTicketsFromRequester($requester);

echo json_encode($result);
