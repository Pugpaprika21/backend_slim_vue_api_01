<?php

namespace Buliders\TypeORM\DB;

use PDO;
use PDOException;

class DBConfig
{
    /**
     * @return PDO|null
     */
    protected static function connect(): ?PDO
    {
        global $database;

        $db = $database['pdo'];
        $dsn = "mysql:host={$db['host']};dbname={$db['name']};charset={$db['char_set']};port={$db['port']}";

        try {
            unset($database);
            return new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}
