<?php

namespace Helper\String;

class Str
{
    private static $setWord = "";
    private static $setInsteadOfWord = "";

    /**
     * #Str::genNumber('U', $rows['user_id'], $rows['user_id']); -> U573
     *
     * @param string $prefix
     * @param integer $number
     * @param integer $length
     * @return string|null
     */
    public static function genNumber($prefix, $number, $length)
    {
        $len = strlen($length);
        $resStr = str_pad($number, $len, '0', STR_PAD_LEFT);
        if ($resStr != '') return $prefix . $resStr;
        return null;
    }

    /**
     * #Str::word($string)->limit($limit);
     *
     * @param string $string
     * @param integer $insteadOfWord
     * @return Str|null
     */
    public static function word($string, $insteadOfWord = "...")
    {
        if (is_string($string)) {
            self::$setWord = $string;
            self::$setInsteadOfWord = $insteadOfWord;
            return new self;
        }
        return null;
    }

    /**
     * #Str::word($string)->limit($limit);
     * 
     * @param integer $limit
     * @return string
     */
    public function limit($limit = 0)
    {
        $string = '';
        if (strlen(self::$setWord) > $limit) {
            $string = substr(self::$setWord, 0, $limit) . self::$setInsteadOfWord;
        }

        self::$setWord = "";
        self::$setInsteadOfWord = "";
        return $string;
    }

    /**
     * Str::email('example@example.com');
     *
     * @param string $email
     * @return mixed
     */
    public static function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}