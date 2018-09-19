<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();

if (isset($_GET['authkey'])) {
    $authkey = $_GET['authkey'];

    $user = $databaseController->read('*', 'user', 1, "authkey = '$authkey'");

    if (array_key_exists(0, $user)) {
        if ($user[0]['role'] <= 1) {
            $result = $user;
        } else {
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $result = $databaseController->read('*', 'user', 1, "id = '$id'");
            } else {
                $result = $user;
            }
        }
    } else {
        $result['error'] = 'Invalid authkey given!';
    }
} else {
    $result['error'] = 'No authkey given!';
}

echo json_encode($result);
