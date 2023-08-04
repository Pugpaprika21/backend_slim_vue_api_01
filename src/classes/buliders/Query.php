<?php

/**
 * @author Pug_DEV <!>
 * 
 * @override <PDO>
 */

namespace Buliders\TypeORM\ORM;

use Buliders\TypeORM\DB\DBConfig;
use Buliders\TypeORM\ORM\Base\Bulider;

class Query extends DBConfig
{
    private static array $setQuery = [];

    /**
     * $db->table('user_tb');
     *
     * @param string $tableName
     * @return self
     */
    public function table(string $tableName): self
    {
        self::$setQuery = ['connect' => self::connect(), 'date' => now('d'),'table' => $tableName];
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
        $conn = $set['connect'];

        $fields = implode(", ", array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";

        $sqlInsert = "insert into {$set['table']}({$fields}) values({$values})";

        write_log($sqlInsert,  __DIR__ . "/../../logs/process/query_{$set['date']}.txt");

        $resultQuery = $conn->query($sqlInsert) ? $conn->lastInsertId() : false;

        unset($set);
        return $resultQuery;
    }

    /**
     * 
     *
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

        $conn = $set['connect'];

        $whereCondi = self::whereWith($set);

        $sqlUpdate = "update {$set['table']} set {$set['fields']} {$whereCondi}";

        write_log($sqlUpdate,  __DIR__ . "/../../logs/process/query_{$set['date']}.txt");

        unset($set);
        return $conn->query($sqlUpdate) ? true : false;
    }

    /**
     * @return boolean
     */
    public static function delete(): bool
    {
        $set = self::$setQuery;

        $conn = $set['connect'];

        $whereCondi = self::whereWith($set);

        $sqlDelete = "delete from {$set['table']} {$whereCondi}";

        write_log($sqlDelete,  __DIR__ . "/../../logs/process/query_{$set['date']}.txt");

        unset($set);
        return $conn->query($sqlDelete) ? true : false;
    }

    /**
     * 
     *
     * @param string $whereCondition
     * @return self
     */
    public static function where(string $whereCondition): self
    {
        self::$setQuery['where'][] = $whereCondition;

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
        $conn = $set['connect'];

        $fields = isset($set['fields']) ? $set['fields'] : "*";

        $whereCondi = self::whereWith($set);
        $orderBy = self::orderByWith($set);

        $sqlSelect = "select {$fields} from {$set['table']} {$whereCondi} {$orderBy}";

        write_log($sqlSelect,  __DIR__ . "/../../logs/process/query_{$set['date']}.txt");

        $resultQuery = $conn->query($sqlSelect)->fetchAll();

        unset($set);
        return $resultQuery;
    }

    /**
     * @param string $sqlStmt
     * @param boolean $fetchWithOutArray
     * @return mixed
     */
    public function excute(string $sqlStmt, bool $fetchWithOutArray = true): mixed
    {
        $conn = self::connect();

        if (preg_match('/^SELECT/i', $sqlStmt)) {

            $items = $conn->query($sqlStmt)->fetchAll();

            if ($fetchWithOutArray) {
                return $items;
            }

            return !empty($items[0]) ? $items[0] : $items;
        }

        if (preg_match('/^INSERT/i', $sqlStmt) || preg_match('/^UPDATE/i', $sqlStmt) || preg_match('/^DELETE/i', $sqlStmt)) {
            self::$setQuery = ['connect' => $conn, 'stmt' => $sqlStmt];
        }

        unset($conn);
        return new self;
    }

    /**
     * @return boolean
     */
    public static function query(): bool
    {
        $set = self::$setQuery;

        $conn = $set['connect'];

        $resultQuery = $conn->query($set['stmt']) ? true : false;

        unset($set);
        return $resultQuery;
    }

    /**
     * #create instance Bulider object
     * @return Bulider
     */
    public static function bulider(): Bulider
    {
        return Bulider::create(['connect' => self::connect()]);
    }
}
