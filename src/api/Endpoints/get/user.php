<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use api\Helpers\ApiOutputHelper;

ApiOutputHelper::setJsonHeader();

$databaseController = ApiOutputHelper::createDatabaseConnection();

if (isset($_GET['authkey'])) {
    $authkey = $_GET['authkey'];

    $user = $databaseController->read('*', 'user', 1, "authkey = '$authkey'");

    if (array_key_exists(0, $user)) {
        if (isset($_GET['userid'])) {
            $userid = $_GET['userid'];
            $userResult = $databaseController->read('*', 'user', 1, "id = '$userid'");
            if ($userResult[0]['role'] > 1) {
                $result = $userResult;
            } elseif ($userResult === $user) {
                $result = $userResult;
            } else {
                $result['error'] = 'You do not have access to this user!';
            }
        } else {
            $result = $user;
        }
    } else {
        $result['error'] = 'Invalid authkey given!';
    }
} else {
    $result['error'] = 'No authkey given!';
}

echo json_encode($result);
