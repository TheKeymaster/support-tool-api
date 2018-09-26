<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$outputController = new OutputController();

$result = $outputController->getAllRoles();

echo json_encode($result);
