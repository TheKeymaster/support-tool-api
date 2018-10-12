<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Controllers\OutputController;
use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setHeaders();

$databaseController = ApiOutputHelper::createDatabaseConnection();
$outputController = new OutputController();

// Set defaults for values that might be empty
$email = isset($_POST['email']) ? $_POST['email'] : false;
$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : false;
$lastname = isset($_POST['lastname']) ? $_POST['lastname'] : false;
$password = isset($_POST['password']) ? $_POST['password'] : false;

if ($email && $firstname && $lastname && $password) {
    $userExists = $databaseController->execCustomSqlQuery("SELECT * FROM user WHERE email = '$email'");
    if ($userExists) {
        $result['result'] = false;
    } else {
        $authkey = $outputController->generateAuthKey();
        $password = password_hash($password, PASSWORD_BCRYPT);
        $result = $databaseController->create('user', ['email', 'firstname', 'lastname', 'password', 'role', 'authkey'], [$email, $firstname, $lastname, $password, 1, $authkey]);
        $result['result'] = true;
    }
} else {
    $result['result'] = false;
}

echo json_encode($result);
