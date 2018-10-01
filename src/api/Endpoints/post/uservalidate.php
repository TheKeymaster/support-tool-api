<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();
$outputController = new \api\Controllers\OutputController();

// Set defaults for values that might be empty
$_POST['ismobile'] = isset($_POST['ismobile']) ? $_POST['ismobile'] : null;
$_POST['password'] = isset($_POST['password']) ? $_POST['password'] : null;
$_POST['email'] = isset($_POST['email']) ? $_POST['email'] : '';

$ismobile = $outputController->isUserMobile($_POST['ismobile']);
$requestedUser = $databaseController->getUserByEmail($_POST['email']);

if ($outputController->isPasswordValid($_POST['password'], $requestedUser['password'], $requestedUser['role'], $ismobile)) {
    $result['result'] = true;
    $result['authkey'] = $requestedUser['authkey'];
} else {
    $result['result'] = false;
}

echo json_encode($result);
