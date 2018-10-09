<?php

namespace api\Controllers;

use api\Helpers\ApiOutputHelper;
use DateTime;

class OutputController
{
    const TICKET_OPEN = 1;
    const TICKET_WAITING = 2;
    const TICKET_CLOSED = 3;

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
     * @param string $query
     * @return array
     */
    public function getTicketsFromRequester($requester, $query)
    {
        if (array_key_exists(0, $requester)) {
            $userId = $requester[0]['id'];
            $escapedQuery = $this->databaseController->escapeStringForSql($query);
            if (is_numeric($escapedQuery)) {
                $query = "WHERE id=$escapedQuery";
            } else {
                $query = strlen($query) > 0 ? "WHERE title LIKE '%$escapedQuery%'" : '';
            }

            if ($requester[0]['role'] <= 1) {
                $tickets = $this->databaseController->execCustomSqlQuery("SELECT id, title, createdby, status FROM tickets WHERE tickets.createdby = '$userId' ORDER BY status ASC, id");
            } else {
                $tickets = $this->databaseController->execCustomSqlQuery("SELECT id, title, createdby, status FROM tickets $query ORDER BY status ASC, id");
            }

            foreach ($tickets as $ticket) {
                if ($ticket['status'] <= 2) {
                    $ticketId = $ticket['id'];

                    $messages = $this->databaseController->execCustomSqlQuery("SELECT * FROM messages WHERE ticketid=$ticketId ORDER BY createdat DESC");
                    $messageCreationDate = new DateTime($messages[0]['createdat']);
                    $now = new DateTime();
                    if ($messageCreationDate->diff($now)->days > 3) {
                        $closed = self::TICKET_CLOSED;
                        $this->databaseController->execCustomSqlQuery("UPDATE tickets SET status=$closed");
                    }
                }
            }

            return $tickets;

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
                    foreach($messages as $key => $message) {
                        $userId = $message['createdby'];
                        $messages[$key]['createdby'] = $this->getUserFirstAndLastnameById($userId);
                    }
                    return $messages;
                }
            } else {
                $messages = $this->databaseController->execCustomSqlQuery("SELECT tickets.title, tickets.status, m.createdat, m.createdby, m.body, m.isinternal FROM tickets LEFT JOIN messages m ON tickets.id = m.ticketid WHERE tickets.id = '$ticketId'");
                foreach($messages as $key => $message) {
                    $userId = $message['createdby'];
                    $messages[$key]['createdby'] = $this->getUserFirstAndLastnameById($userId);
                }
                return $messages;
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

    /**
     * Creates a new message.
     *
     * @param string $createdat String as DateTime readable for the database.
     * @param int $createdby Id from the user that created this message.
     * @param int $ticketid Id in which this message should be created.
     * @param string $body Message body.
     * @param bool $isinternal If the message if true or false 0-1 based (tinyint).
     * @param bool $ticketCreation If this message was made because a ticket was created.
     * @param array $requester The user that created this message.
     * @return array
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function createNewMessage($createdat, $createdby, $ticketid, $body, $isinternal, $ticketCreation, $requester)
    {
        if ($isinternal === 0) {
            $creator = sprintf('%s %s', $requester['firstname'], $requester['lastname']);
            switch($requester['role']) {
                case 1:
                    $this->sendMessageToAllSupportersAndAdmins($ticketid, $ticketCreation, $creator);
                    $this->sendMessageToUser($ticketid, $ticketCreation);
                    $this->setTicketStatus($ticketid, self::TICKET_OPEN);
                    break;
                case 2:
                case 3:
                    $this->sendMessageToUser($ticketid, $ticketCreation);
                    $this->setTicketStatus($ticketid, self::TICKET_WAITING);
                    break;
            }
        }
        return $this->databaseController->create('messages', ['createdat', 'createdby', 'ticketid', 'body', 'isinternal'],
            [$createdat, $createdby, $ticketid, $body, $isinternal]);
    }

    /**
     * @param int $ticketid
     * @param string $status
     */
    private function setTicketStatus($ticketid, $status)
    {
        $this->databaseController->execCustomSqlQuery("UPDATE tickets SET status='$status' WHERE id=$ticketid");
    }

    /**
     * @param $ticketid
     * @param bool $ticketCreation If this message was made because a ticket was created.
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendMessageToAllSupportersAndAdmins($ticketid, $ticketCreation, $creator)
    {
        $supportersAndAdmins = $this->databaseController->execCustomSqlQuery("SELECT * FROM user WHERE role > 1");
        $mailController = new MailController();
        foreach ($supportersAndAdmins as $supporterOrAdmin) {
            $name = sprintf('%s %s', $supporterOrAdmin['firstname'], $supporterOrAdmin['lastname']);
            $mailController->addAddress($supporterOrAdmin['email'], $name);
        }
        if ($ticketCreation) {
            $mailController->sendMail('Ein neues Ticket wurde erstellt!',
                'ticket-created-admin.twig', ['name' => $creator, 'id' => $ticketid]);
        } else {
            $mailController->sendMail('Es gibt eine neue Nachricht in einem Ticket!',
                'message-created-admin.twig', ['name' => $creator, 'id' => $ticketid]);
        }
    }

    /**
     * @param $ticketid
     * @param $ticketCration
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendMessageToUser($ticketid, $ticketCration)
    {
        $ticketCratorId = $this->databaseController->execCustomSqlQuery("SELECT * FROM tickets WHERE id = '$ticketid'")[0]['createdby'];
        $creator = $this->databaseController->execCustomSqlQuery("SELECT * FROM user WHERE id = '$ticketCratorId'")[0];
        $name = sprintf('%s %s', $creator['firstname'], $creator['lastname']);
        $mailController = new MailController();
        $mailController->addAddress($creator['email'], $name);
        if ($ticketCration) {
            $mailController->sendMail('Ein neues Ticket wurde erstellt!',
                'ticket-created-user.twig', ['name' => $name, 'id' => $ticketid]);
        } else {
            $mailController->sendMail('Es gibt eine neue Nachricht in einem Ticket!',
                'message-created-user.twig', ['name' => $name, 'id' => $ticketid]);
        }
    }

    private function getUserFirstAndLastnameById($userId)
    {
        $user = $this->databaseController->execCustomSqlQuery("SELECT firstname, lastname FROM user WHERE id = $userId");
        return sprintf('%s %s', $user[0]['firstname'], $user[0]['lastname']);
    }
}