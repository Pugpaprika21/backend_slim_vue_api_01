<?php

namespace App\Foundation\Database;

use Exception;
use PDO;
use PDOException;

class QueryConnection
{
    use QueryConnectionTrait;

    /**
     * @param string $driver
     * @return array|null
     * @throws Exception
     */
    private function config(string $driver): ?array
    {
        $realPath = __DIR__ . '../../../../config/database.php';
        $db = require($realPath);

        if (isset($db['connection'])) {
            $config = $db['connection'][$driver];
            if (!isset($config)) {
                throw new Exception("Driver config not found in: {$realPath}");
            }

            return $config;
        }

        throw new Exception("Database config not found in: {$realPath}");
    }

    /**
     * @param string $driver
     * @return PDO|null
     * @throws PDOException
     */
    protected function connect(string $driver): ?PDO
    {
        $db = $this->config($driver);
        $dsn = $this->getDriverDNS($db, $driver);

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            return new PDO($dsn, $db['user'], $db['pass'], $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}
