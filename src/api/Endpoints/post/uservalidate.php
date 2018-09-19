<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email && $password) {
        $dbResult = $databaseController->read('*', 'user', 1, "email = '$email'");
        $result['result'] = password_verify($password, $dbResult[0]['password']);
        $result['authkey'] = $dbResult[0]['authkey'];
    }
} else {
    $result = ['result' => false];
}

echo json_encode($result);