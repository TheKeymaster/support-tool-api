<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();
$outputController = new \api\Controllers\OutputController();

$authkey = $outputController->generateAuthKey();

// Set defaults for values that might be empty
$_POST['email'] = isset($_POST['email']) ? $_POST['email'] : false;
$_POST['firstname'] = isset($_POST['firstname']) ? $_POST['firstname'] : false;
$_POST['lastname'] = isset($_POST['lastname']) ? $_POST['lastname'] : false;
$_POST['password'] = isset($_POST['password']) ? $_POST['password'] : false;

$ismobile = $outputController->isUserMobile($_POST['ismobile']);
$requestedUser = $databaseController->getUserByEmail($_POST['email']);

if ($outputController->isPasswordValid($_POST['password'], $requestedUser['password'], $requestedUser['role'], $ismobile)) {
    $result['result'] = true;
    $result['authkey'] = $requestedUser['authkey'];
} else {
    $result = ['result' => false];
}

echo json_encode($result);
