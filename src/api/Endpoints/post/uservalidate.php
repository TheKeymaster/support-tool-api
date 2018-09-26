<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();

if (isset($_POST['ismobile']) && $_POST['ismobile'] == true) {
    $ismobile = true;
} else {
    $ismobile = false;
}

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $dbResult = $databaseController->read('*', 'user', 1, "email = '$email'");
    $result['result'] = password_verify($password, $dbResult[0]['password']);

    // Only send the authkey if the password is valid.
    if ($result['result']) {
        if ($ismobile === true && $dbResult[0]['role'] > 1) {
            $result['result'] = false;
        } else {
            $result['authkey'] = $dbResult[0]['authkey'];
        }
    }
} else {
    $result = ['result' => false];
}

echo json_encode($result);
