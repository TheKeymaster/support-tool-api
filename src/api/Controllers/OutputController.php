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
        $result = $this->databaseController->getUserById($userId);

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

    /**
     * Gets all tickets from the requester.
     *
     * @param $requester
     * @return array
     */
    public function getTicketsFromRequester($requester)
    {
        if (array_key_exists(0, $requester)) {
            $userId = $requester[0]['id'];

            if ($requester[0]['role'] <= 1) {
                return $this->databaseController->execCustomSqlQuery("SELECT id, title, status FROM tickets WHERE tickets.createdby = '$userId'");
            } else {
                return $this->databaseController->execCustomSqlQuery("SELECT id, title, status FROM tickets");
            }

        } else {
            return ['error' => SecurityController::INVALID_AUTHKEY];
        }
    }

    /**
     * Gets the messages for the current ticket id.
     *
     * @param array $requester
     * @param int $ticketId
     * @return array
     */
    public function getMessagesFromRequesterAndTicketId($requester, $ticketId)
    {
        if (array_key_exists(0, $requester)) {
            $userId = $requester[0]['id'];
            if ($requester[0]['role'] <= 1) {
                $messages = $this->databaseController->execCustomSqlQuery("SELECT tickets.title, tickets.status, m.createdat, m.createdby, m.body, m.isinternal FROM tickets LEFT JOIN messages m ON tickets.id = m.ticketid WHERE tickets.createdby = '$userId' AND tickets.id = '$ticketId' AND m.isinternal = 0");
                if (count($messages) < 1) {
                    return ['error' => SecurityController::ACCESS_DENIED];
                } else {
                    return $messages;
                }
            } else {
                return $this->databaseController->execCustomSqlQuery("SELECT tickets.title, tickets.status, m.createdat, m.createdby, m.body, m.isinternal FROM tickets LEFT JOIN messages m ON tickets.id = m.ticketid WHERE tickets.id = '$ticketId'");
            }
        } else {
            return ['error' => SecurityController::INVALID_AUTHKEY];
        }
    }

    /**
     * Checks if the current password is valid. If a supporter tries to login on mobile, this will return false.
     *
     * @param $password
     * @param $hashedPassword
     * @param $role
     * @param $isMobile
     * @return bool
     */
    public function isPasswordValid($password, $hashedPassword, $role, $isMobile)
    {
        $passwordIsValid = password_verify($password, $hashedPassword);

        // Only send the authkey if the password is valid.
        if ($passwordIsValid) {
            if ($isMobile && $role > 1) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Checks if the mobile flag is actually true or something else.
     *
     * @param bool|null $flag
     * @return bool
     */
    public function isUserMobile($flag)
    {
        if ($flag == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getAllRoles()
    {
        return $this->databaseController->read('*', 'roles');
    }

    /**
     * Generates a new auth key.
     *
     * @return string
     */
    public function generateAuthKey()
    {
        return implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));
    }
}