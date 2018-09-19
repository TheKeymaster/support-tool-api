<?php

namespace api\Controllers;

use InvalidArgumentException;
use mysqli;
use RuntimeException;

class DatabaseController
{
    /** @var string */
    const DB_NAME = 'support_system';

    /** @var string */
    const DB_ERROR_MESSAGE = 'An unknown error occurred!';

    /** @var string */
    const STATUS_SUCCESS = 'success';

    /** @var string */
    const STATUS_ERROR = 'error';

    /** @var string $host */
    private $host;

    /** @var string $username */
    private $username;

    /** @var string $password */
    private $password;

    /** @var mysqli $mysqli */
    private $mysqli;

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

        $mysqli = new mysqli($host, $username, $password, self::DB_NAME);

        if ($mysqli->connect_error) {
            throw new RuntimeException(sprintf(
                'Connection to database failed. Reason: %s',
                $mysqli->connect_error
            ));
        }

        $mysqli->set_charset('utf8');
        $mysqli->query('SET CHARACTER SET utf8');

        $this->mysqli = $mysqli;
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Creates a new entry in a specific table.
     *
     * @param string $tableName
     * @param array $fields
     * @param array $values
     * @return array
     */
    public function create($tableName, array $fields, array $values)
    {
        if (!is_string($tableName)) {
            throw new InvalidArgumentException('Invalid type for table name submitted!');
        }

        $query = "INSERT INTO $tableName ($fields) VALUES ($values)";
        if ($this->mysqli->query($query)) {
            $successMessage = 'Row(s) were successfully created!';
            $affectedRows = $this->mysqli->affected_rows;
            return $this->createStatusMessage(self::STATUS_SUCCESS, $successMessage, $affectedRows);
        } else {
            return $this->unknownDatabaseError();
        }
    }

    /**
     * Reads data from a specific table.
     *
     * @param string|array $cols
     * @param string $tableName
     * @param int $limit
     * @param string|null $condition Condition without "where" e.g. 'id = 123'.
     * @return array
     */
    public function read($cols, $tableName, $limit = 1000, $condition = null)
    {
        if (!is_string($tableName)) {
            throw new InvalidArgumentException('Invalid type for table name submitted!');
        }

        if ($condition === null) {
            $query = "SELECT $cols FROM $tableName LIMIT $limit";
        } elseif (is_string($condition)) {
            $query = "SELECT $cols FROM $tableName WHERE $condition LIMIT $limit";
        } else {
            throw new InvalidArgumentException('Invalid type for condition submitted!');
        }

        if ($result = $this->mysqli->query($query)) {
            if ($result->num_rows > 0) {
                return $result->fetch_all(MYSQLI_ASSOC);
            } else {
                $message = 'No results are matching your params! Make sure you have set the correct authkey!';
                return $this->createStatusMessage(self::STATUS_SUCCESS, $message, 0);
            }
        } else {
            return $this->unknownDatabaseError();
        }
    }

    /**
     * Updates data from a specific table.
     *
     * @param string $tableName
     * @param string $set Condition without "set" e.g. 'lastname="Joe"'.
     * @param string $condition Condition without "where" e.g. 'id = 123'.
     * @return array
     */
    public function update($tableName, $set, $condition)
    {
        if (!(is_string($tableName) && is_string($set) && is_string($condition))) {
            throw new InvalidArgumentException('Type of table name, set and/or where is invalid!');
        }

        $query = "UPDATE $tableName SET $set WHERE $condition";

        if ($this->mysqli->query($query)) {
            $message = 'Row(s) were updated successfully!';
            return $this->createStatusMessage(self::STATUS_SUCCESS, $message, $this->mysqli->affected_rows);
        } else {
            return $this->unknownDatabaseError();
        }
    }

    /**
     * Deletes data from a specific table.
     *
     * @param $tableName
     * @param $condition
     * @return array
     */
    public function delete($tableName, $condition)
    {
        if (!(is_string($tableName) && is_string($condition))) {
            throw new InvalidArgumentException('Type of table name, set and/or where is invalid!');
        }

        $query = "DELETE FROM $tableName WHERE $condition";

        if ($this->mysqli->query($query)) {
            $message = 'Row(s) were deleted successfully!';
            return $this->createStatusMessage(self::STATUS_SUCCESS, $message, $this->mysqli->affected_rows);
        } else {
            return $this->unknownDatabaseError();
        }
    }

    /**
     * Executes a custom sql command.
     *
     * @param $query
     * @return array
     */
    public function execCustomSqlQuery($query)
    {
        if (!is_string($query)) {
            throw new InvalidArgumentException('Type of command is invalid!');
        }

        if ($result = $this->mysqli->query($query)) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return $this->unknownDatabaseError();
        }
    }

    /**
     * This function is being called if the SQL query returns an error.
     *
     * @return array
     */
    private function unknownDatabaseError()
    {
        return $this->createStatusMessage(self::STATUS_ERROR, self::DB_ERROR_MESSAGE, 0);
    }

    /**
     * @param string $status
     * @param string $message
     * @param int $affectedRows
     * @return array
     */
    private function createStatusMessage($status, $message, $affectedRows)
    {
        return [
            'status' => $status,
            'message' => $message,
            'rows_affected' => $affectedRows,
        ];
    }
}
