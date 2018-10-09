<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setHeaders();

$databaseController = ApiOutputHelper::createDatabaseConnection();
$outputController = new OutputController();

$_GET['authkey'] = isset($_GET['authkey']) ? $_GET['authkey'] : null;
$_GET['query'] = isset($_GET['query']) ? $_GET['query'] : '';

$requester = $outputController->sendAuthkeyRequest($_GET['authkey']);
$result = $outputController->getTicketsFromRequester($requester, $_GET['query']);

echo json_encode($result);
