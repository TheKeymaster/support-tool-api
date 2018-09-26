<?php

namespace api\Controllers;


use api\Helpers\ApiOutputHelper;

class OutputController
{
    private $databaseController;

    private $securityController;

    public function __construct()
    {
        $this->databaseController = ApiOutputHelper::createDatabaseConnection();
        $this->securityController = new SecurityController();
    }

    /**
     * Gets data from the database and returns the user by userid. If the requester does not have
     * enough permission, an error will be returned.
     *
     * @param int $userId
     * @param array $requester
     * @return array
     */
    private function getDataByUserId($userId, $requester)
    {
        $result = $this->databaseController->execCustomSqlQuery("SELECT email, firstname, lastname, role FROM user WHERE id = '$userId'");

        if ($this->securityController->isAllowedToSeeUserData($requester[0], $result[0])) {
            return $result;
        } else {
            return ['error' => SecurityController::ACCESS_DENIED];
        }
    }

    /**
     * Requests the database to get the current user by id. If the id is not specified, the
     * requester will be returned as a result.
     *
     * @param $requester
     * @return array
     */
    public function sendUserIdRequest($requester)
    {
        if (array_key_exists(0, $requester)) {
            if (isset($_GET['userid']) && $_GET['userid'] !== '') {
                $userId = $_GET['userid'];
                return $this->getDataByUserId($userId, $requester);
            } else {
                return $requester;
            }
        } else {
            return ['error' => SecurityController::INVALID_AUTHKEY];
        }
    }

    /**
     * Requests the database to get the current user by the authkey. If the authkey is not specified or
     * empty, an error will be returned.
     *
     * @param $authkey
     * @return array
     */
    public function sendAuthkeyRequest($authkey)
    {
        if (isset($_GET['authkey']) && $_GET['authkey'] !== '') {
            return $this->databaseController->getUserByAuthkey($authkey);
        } else {
            return ['error' => SecurityController::INVALID_AUTHKEY];
        }
    }
}