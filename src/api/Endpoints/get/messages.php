<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setHeaders();

$outputController = new OutputController();

$_GET['authkey'] = isset($_GET['authkey']) ? $_GET['authkey'] : null;
$_GET['ticketid'] = isset($_GET['ticketid']) ? $_GET['ticketid'] : null;

$requester = $outputController->sendAuthkeyRequest($_GET['authkey']);
$result = $outputController->getMessagesFromRequesterAndTicketId($requester, $_GET['ticketid']);

foreach ($result as $key => $value) {
    $defaultUrl = urlencode('//support-tool.brader.co.at/web/images/default.jpg');
    $result[$key]['imageurl'] = sprintf('https://secure.gravatar.com/avatar/%s?d=%s&s=80', md5($result[$key]['email']), $defaultUrl);
}

echo json_encode($result);
