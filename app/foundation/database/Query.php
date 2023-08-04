<?php

namespace App\Foundation\Database;

use PDO;

class Query extends QueryConnection
{
    private static ?PDO $db;
    private static array $setQuery = [];

    use QueryBuliderTrait;

    public function __construct(string $setDriver = 'mysql')
    {
        self::$db = $this->connect($setDriver);
    }

    public static function table(string $table): self
    {
        self::$setQuery['db'] = self::$db;
        self::$setQuery['table'] = $table;
        self::$setQuery['date'] = now();
        self::$setQuery['save_log'] = self::$logQuery;

        return new self;
    }

    /**
     * $db->table('user_tb')->insert(array());
     *
     * @param array $data
     * @return integer|bool
     */
    public static function insert(array $data): int|bool
    {
        $set = self::$setQuery;
        $conn = $set['db'];

        $fields = implode(", ", array_keys($data));
        $values = implode(", ", array_fill(0, count($data), "?"));

        $sqlInsert = "insert into {$set['table']} ({$fields}) values ({$values})";

        write_log($sqlInsert, $set['save_log'] . "query_{$set['date']}.txt");

        $stmt = $conn->prepare($sqlInsert);
        $resultQuery = $stmt->execute(array_values($data)) ? $conn->lastInsertId() : false;

        unset($set, $stmt);
        return $resultQuery;
    }

    /**
     * @return self
     */
    public static function select(): self
    {
        $fieldValid = func_get_args();
        $fieldToString = join(', ', $fieldValid);

        self::$setQuery['fields'] = count($fieldValid) > 0 ? $fieldToString : "*";

        return new self;
    }

    /**
     * @param array $fields
     * @return self
     */
    public static function fields(array $fields): self
    {
        $sqlUpdate = '';
        $updates = array();
        foreach ($fields as $column => $value) {
            $updates[] = "{$column} = '{$value}'";
        }
        $sqlUpdate .= implode(', ', $updates);

        self::$setQuery['fields'] = $sqlUpdate;

        return new self;
    }

    /**
     * @return boolean
     */
    public static function update(): bool
    {
        $set = self::$setQuery;

        $conn = $set['db'];

        $whereCondi = self::whereWith($set);

        $sqlUpdate = "update {$set['table']} set {$set['fields']} {$whereCondi}";

        write_log($sqlUpdate, $set['save_log'] . "query_{$set['date']}.txt");

        return self::excuteRawQuery($conn, $sqlUpdate);
    }

    /**
     * @return boolean
     */
    public static function delete(): bool
    {
        $set = self::$setQuery;

        $conn = $set['db'];

        $whereCondi = self::whereWith($set);

        $sqlDelete = "delete from {$set['table']} {$whereCondi}";

        write_log($sqlDelete, $set['save_log'] . "query_{$set['date']}.txt");

        unset($set);
        return self::excuteRawQuery($conn, $sqlDelete);
    }

    /**
     * @param string $field
     * @param string $condition
     * @param string|int $value
     * @return self
     */
    public static function where(string $field, string $condition, string|int $value): self
    {
        self::$setQuery['where'][] = "{$field} {$condition} '{$value}'";

        return new self;
    }

    /**
     * @param string $fieldsOrderBy
     * @param string $orderBy
     * @return self
     */
    public static function orderBy(string $fieldsOrderBy, string $orderBy = 'desc'): self
    {
        $chkOrderBy = $orderBy ? $orderBy : "";
        self::$setQuery['orderBy'] = "order by {$fieldsOrderBy} {$chkOrderBy}";

        return new self;
    }

    /**
     * @param array $set
     * @return string
     */
    private static function whereWith(array $set): string
    {
        $whereValid = isset($set['where']) ? $set['where'] : [];
        $whereCondi = count($whereValid) > 0 ? "where " . join(' and ', $set['where']) : "where 1=1 ";
        return $whereCondi;
    }

    /**
     * @param array $set
     * @return string
     */
    private static function orderByWith(array $set): string
    {
        return isset($set['orderBy']) ? $set['orderBy'] : "";
    }

    /**
     * @return array
     */
    public static function get()
    {
        $set = self::$setQuery;
        $conn = $set['db'];

        $fields = isset($set['fields']) ? $set['fields'] : "*";

        $whereCondi = self::whereWith($set);
        $orderBy = self::orderByWith($set);

        $sqlSelect = "select {$fields} from {$set['table']} {$whereCondi} {$orderBy}";

        write_log($sqlSelect, $set['save_log'] . "query_{$set['date']}.txt");

        $resultQuery = $conn->query($sqlSelect)->fetchAll();

        unset($set, $conn);
        return $resultQuery;
    }

    /**
     * @param string $sqlStmt
     * @param boolean $fetchWithoutArray
     * @return mixed
     */
    public function excute(string $sqlStmt, bool $fetchWithoutArray = true): mixed
    {
        $conn = self::$db;
        if (preg_match('/^SELECT/i', $sqlStmt)) {
            $items = $conn->query($sqlStmt)->fetchAll();
            if ($fetchWithoutArray) return $items;
            return !empty($items[0]) ? $items[0] : $items;
        }

        if (preg_match('/^INSERT/i', $sqlStmt) || preg_match('/^UPDATE/i', $sqlStmt) || preg_match('/^DELETE/i', $sqlStmt)) {
            return self::excuteRawQuery($conn, $sqlStmt);
        }
        return self::excuteRawQuery($conn, $sqlStmt);
    }
}
