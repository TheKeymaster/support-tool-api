<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Controllers\SecurityController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$outputController = new OutputController();

$_GET['authkey'] = isset($_GET['authkey']) ? $_GET['authkey'] : null;

$requester = $outputController->sendAuthkeyRequest($_GET['authkey']);
$result = $outputController->sendUserIdRequest($requester);
$result[0]['imageurl'] = sprintf('https://www.gravatar.com/avatar/%s', md5($result[0]['email']));

echo json_encode($result);
