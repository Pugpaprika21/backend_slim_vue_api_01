<?php

namespace Buliders\TypeORM\ORM\Base;

use Buliders\TypeORM\ORM\Query;

class Bulider extends Query
{
    protected static array $setQuery = [];

    public static function create(array $set): Bulider
    {
        self::$setQuery = $set;
        return new self;
    }

    public function all()
    {
        return self::$setQuery;
    }
}
