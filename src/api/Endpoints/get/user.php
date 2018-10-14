<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Controllers\SecurityController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setHeaders();

$outputController = new OutputController();

$_GET['authkey'] = isset($_GET['authkey']) ? $_GET['authkey'] : null;

$requester = $outputController->sendAuthkeyRequest($_GET['authkey']);
$result = $outputController->sendUserIdRequest($requester);

$defaultUrl = urlencode('//support-tool.brader.co.at/web/images/default.jpg');
$result[0]['imageurl'] = sprintf('https://secure.gravatar.com/avatar/%s?d=%s&s=80', md5($result[0]['email']), $defaultUrl);

echo json_encode($result);
