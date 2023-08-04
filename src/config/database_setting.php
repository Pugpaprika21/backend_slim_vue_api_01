<?php

return [

    /* 
    * xampp mysqil
    */
    'mysqil' => [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'name' => 'save_job_db',
        'char_set' => 'utf8'
    ],

    /* 
    * pdo mysqil
    */
    'pdo' => [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'name' => 'save_job_db',
        'port' => '3306',
        'char_set' => 'utf8'
    ],

    /* 
    * setup eloquent orm connection for Laravel
    */
    'eloquent' => [
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'save_job_db',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]
];

