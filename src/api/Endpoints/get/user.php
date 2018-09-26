<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Controllers\SecurityController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$outputController = new OutputController();

$requester = $outputController->sendAuthkeyRequest($_GET['authkey']);
$result = $outputController->sendUserIdRequest($requester);

echo json_encode($result);
