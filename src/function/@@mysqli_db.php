<?php

/**
 * @author Pug_DEV <!>
 * 
 * @override <mysqli>
 */

if (!function_exists('db_connect')) {

    /**
     * #db_connect();
     *
     * @return mysqli|bool
     */
    function db_connect(): mysqli
    {
        global $database;

        $db = $database['mysqil'];

        if (isset($database)) {
            $conn = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
            mysqli_set_charset($conn, $db['char_set']);
            unset($db);
            return (!$conn) ? false : $conn;
        }
        return false;
    }
}

if (!function_exists('db_select')) {

    /**
     * #db_select('USER_TB');
     *
     * @param string $tbl_or_sql|$full_select
     * @param string $fields
     * @param string $condi
     * @return array
     */
    function db_select($tbl_or_sql, $fields = '*', $condi = '', $to_arr = false)
    {
        $conn = db_connect();
        $items = [];
        $d = now('d');
        $wheres = ($condi != '') ? "WHERE 1=1 {$condi}" : "";
        $sql_select = "SELECT {$fields} FROM {$tbl_or_sql} {$wheres}";

        if (preg_match('/^SELECT/i', $tbl_or_sql)) {
            unset($sql_select);
            $sql_select = $tbl_or_sql;
        }

        $query_ = mysqli_query($conn, $sql_select);
        write_log($sql_select,  __DIR__ . "/../../logs/process/query_select_{$d}.txt");

        while ($rows = mysqli_fetch_assoc($query_)) {
            array_push($items, $rows);
        }

        mysqli_close($conn);

        if ($to_arr) {
            return $items;
        }

        return !empty($items[0]) ? $items[0] : $items;
    }
}

if (!function_exists('db_insert')) {

    /**
     * #db_insert('USER_TB', ['NAME' => 'alex', 'PASS' => '1234']);
     *
     * @param string $tbl
     * @param array $data
     * @param string $primary
     * @return array|bool
     */
    function db_insert($tbl, $data, $primary = '?')
    {
        $conn = db_connect();
        $fields = implode(", ", array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";

        $d = now('d');
        $sql_insert = "INSERT INTO {$tbl}({$fields}) VALUES({$values})";

        $query_ = mysqli_query($conn, $sql_insert);
        write_log($sql_insert,  __DIR__ . "/../../logs/process/query_insert_{$d}.txt");

        if ($query_) {
            if ($primary != '?') {
                return db_select("SELECT * FROM {$tbl} ORDER BY {$primary} DESC LIMIT 1");
            }

            mysqli_close($conn);
            return true;
        }
        return false;
    }
}

if (!function_exists('db_update')) {

    /**
     * #db_update('USER_TB', ['NAME' => 'alex', 'PASS' => '1234'], "USR_ID = '1'");
     *
     * @param string $tbl
     * @param array $data
     * @param string $condi
     * @return bool
     */
    function db_update($tbl, $data, $condi)
    {
        $fields = '';
        foreach ($data as $fields_ => $values_) {
            $fields .= !empty($fields) ? ', ' : '';
            $fields .= "{$fields_} = '{$values_}'";
        }

        $conn = db_connect();
        $d = now('d');
        $sql_upd = "UPDATE {$tbl} SET {$fields} WHERE {$condi}";
        write_log($sql_upd,  __DIR__ . "/../../logs/process/query_update_{$d}.txt");

        $query_upd = mysqli_query($conn, $sql_upd);

        mysqli_close($conn);
        return ($query_upd) ? true : false;
    }
}

if (!function_exists('db_delete')) {

    /**
     * #db_delete('USER_TB', "USR_ID = '1'");
     *
     * @param string $tbl
     * @param string $condi
     * @return bool
     */
    function db_delete($tbl, $condi)
    {
        $conn = db_connect();
        $sql_del = "DELETE FROM {$tbl} WHERE {$condi}";
        $d = now('d');

        $query_del = mysqli_query($conn, $sql_del);
        write_log($sql_del,  __DIR__ . "/../../logs/process/query_delete_{$d}.txt");

        mysqli_close($conn);
        return ($query_del) ? true : false;
    }
}

if (!function_exists('db_lastId')) {

    /**
     * #db_lastId('user_tb', 'usr_id');
     *
     * @param string $tbl
     * @param string $field_max
     * @return integer
     */
    function db_lastId($tbl, $field_max)
    {
        $conn = db_connect();
        $sql_ld = "SELECT MAX({$field_max}) AS MAX_ID FROM {$tbl}";

        $d = now('d');
        $query_ = mysqli_query($conn, $sql_ld);
        write_log($sql_ld,  __DIR__ . "/../../logs/process/query_select_{$d}.txt");

        mysqli_close($conn);

        if ($query_) {
            $max_res = mysqli_fetch_assoc($query_);
            unset($query_);
            return $max_res['MAX_ID'];
        }
        return 0;
    }
}

if (!function_exists('db_rows')) {

    /**
     * #db_rows('USER_TB');
     *
     * @param string $tbl
     * @return string|int
     */
    function db_rows($tbl)
    {
        $conn = db_connect();
        $rows_ = 0;
        $sql_num = "SELECT * FROM {$tbl}";
        $d = now('d');

        $qurey_ = mysqli_query($conn, $sql_num);
        write_log($sql_num,  __DIR__ . "/../../logs/process/query_select_{$d}.txt");

        if ($qurey_) {
            mysqli_free_result($qurey_);
            return mysqli_num_rows($qurey_);
        }
        mysqli_close($conn);
        return $rows_;
    }
}

if (!function_exists('db_query')) {

    /**
     * #db_query('select ...stmt');
     *
     * @param string $sql_stmt
     * @param bool|callable $fetch_auto_array
     * @return mixed
     */
    function db_query($sql_stmt, $fetch_auto_array = true)
    {
        $conn = db_connect();
        $query_ = mysqli_query($conn, $sql_stmt);

        if (!$query_) {
            mysqli_close($conn);
            return false;
        }

        $d = now('d');
        write_log($sql_stmt,  __DIR__ . "/../../logs/process/query_{$d}.txt");

        if (preg_match('/^SELECT/i', $sql_stmt)) {

            $items = array();
            while ($rows = mysqli_fetch_assoc($query_)) {
                $items[] = $rows;
            }

            if ((count($items) > 1)) {
                $rows_result = $items;
            } else {
                $rows_result = !empty($items[0]) ? $items[0] : $items;
            }

            mysqli_close($conn);

            if (is_callable($fetch_auto_array)) {
                return $fetch_auto_array($rows_result, $sql_stmt);
            } elseif ($fetch_auto_array) {
                return $rows_result;
            }
            return $query_;
        } elseif (preg_match('/^INSERT/i', $sql_stmt) || preg_match('/^UPDATE/i', $sql_stmt) || preg_match('/^DELETE/i', $sql_stmt)) {
            $affected_rows = mysqli_affected_rows($conn);
            mysqli_close($conn);
            return ($affected_rows > 0);
        }
        mysqli_close($conn);
        return false;
    }
}

if (!function_exists('db_last_row')) {

    /**
     * #db_last_row("user_tb", "user_id");
     *
     * @param string $tbl
     * @param string $primary
     * @return array
     */
    function db_last_row($tbl, $primary)
    {
        $d = now('d');
        write_log($tbl,  __DIR__ . "/../../logs/process/query_select_{$d}.txt");
        return db_select("SELECT * FROM {$tbl} ORDER BY {$primary} DESC LIMIT 1");
    }
}

if (!function_exists('db_fetch_array')) {

    /**
     * #db_fetch_array("select * from user_tb");
     *
     * @param string $tbl_or_sql
     * @return array|null
     */
    function db_fetch_array($tbl_or_sql)
    {
        if (preg_match('/^SELECT/i', $tbl_or_sql)) {
            $d = now('d');
            write_log($tbl_or_sql,  __DIR__ . "/../../logs/process/query_select_{$d}.txt");
            return db_select($tbl_or_sql, '', '', true);
        }

        return null;
    }
}
