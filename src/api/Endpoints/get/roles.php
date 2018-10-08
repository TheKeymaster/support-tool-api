<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\MailController;
use api\Controllers\OutputController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setHeaders();

$outputController = new OutputController();

$result = $outputController->getAllRoles();
$mailController = new MailController();

$mailController->sendMail('dominik@brader.co.at', 'Dominik Brader', 'Testmail', 'test.twig');

echo json_encode($result);
