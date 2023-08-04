<?php

namespace App\Foundation\Database;

trait QueryConnectionTrait
{
    /**
     * @param array $db
     * @param string $driver
     * @return string
     */
    private function getDriverDNS(array $db, string $driver = 'mysql'): string
    {
        return [
            'mysql' => "mysql:host={$db['host']};dbname={$db['name']};charset={$db['char_set']};port={$db['port']}",
            'sqlsrv' => "sqlsrv:Server={$db['host']},{$db['port']};Database={$db['name']}",
            'pgsql' => "pgsql:host={$db['host']};dbname={$db['name']};charset={$db['char_set']};port={$db['port']}",
            'oci' => "oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST={$db['host']})(PORT={$db['port']}))(CONNECT_DATA=(SERVICE_NAME={$db['name']})));charset={$db['char_set']}"
        ][$driver];
    }
}
