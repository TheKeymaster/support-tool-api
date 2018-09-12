<?php

namespace src\api\Controllers;

use InvalidArgumentException;
use mysqli;

class DatabaseController
{
    /** @var string */
    const DB_NAME = 'support_system';

    /** @var string $host */
    private $host;

    /** @var string $username */
    private $username;

    /** @var string $password */
    private $password;

    /** @var mysqli $connection */
    private $connection;

    /**
     * DatabaseController constructor.
     * @param string $host Database server name.
     * @param string $username Database username.
     * @param string $password Database password.
     */
    public function __construct($host, $username, $password)
    {
        if (!(is_string($username) && is_string($password) && is_string($host))) {
            throw new InvalidArgumentException('Type of host, username and/or password is invalid!');
        }

        $conn = new mysqli($host, $username, $password, self::DB_NAME);

        if ($conn->connect_error) {
            throw new InvalidArgumentException(sprintf('Connection to database failed. Reason: %s',
                $conn->connect_error));
        }

        $this->connection = $conn;
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }
}
