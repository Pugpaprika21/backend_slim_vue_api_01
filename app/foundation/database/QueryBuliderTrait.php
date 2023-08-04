<?php

namespace App\Foundation\Database;

use PDO;
use PDOException;

trait QueryBuliderTrait
{
    private static string $logQuery = __DIR__ . ".../../../../logs/process/";
    /**
     * @param PDO $conn
     * @param string $sqlStmt
     * @return boolean
     */
    private static function excuteRawQuery(PDO $conn, string $sqlStmt): bool
    {
        if ($conn instanceof PDO) {
            return $conn->query($sqlStmt) ? true : false;
        }
        throw new PDOException("Error Class PDO NotFound...", 1);
    }
}
