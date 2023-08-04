<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default and config Database Connection Here!!
    |--------------------------------------------------------------------------
    |
    */
    'connection' => [
        'mysql' => [
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'name' => 'save_job_db',
            'port' => '3306',
            'char_set' => 'utf8',
        ],

        'sqlsrv' => [
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'name' => '',
            'port' => '3306',
            'char_set' => 'utf8',
        ],

        'pgsql' => [
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'name' => '',
            'port' => '3306',
            'char_set' => 'utf8',
        ],

        'oci' => [
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'name' => '',
            'port' => '3306',
            'char_set' => 'utf8',
        ],

        /*
        |--------------------------------------------------------------------------
        |  setup create option date and time to table
        |--------------------------------------------------------------------------
        |
        */
        'create_option' => [
            'create_status' => true,
            'create_time_at' => CREATE_TIME_AT,
            'create_date_at' => CREATE_DATE_AT
        ],

        /*
        |--------------------------------------------------------------------------
        |  setup eloquent orm connection for Laravel
        |--------------------------------------------------------------------------
        |
        */
        'eloquent' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => '',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ]
];
