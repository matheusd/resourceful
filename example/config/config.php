<?php
/************************************
 *  THIS IS A SAMPLE CONFIG FILE
 ************************************/
 
$env = [
    'publicUrl' => 'http://localhost:8039',
    'devVersion' => true,
    
    'databases' => [
        'default' => [
            'driver' => 'pdo_pgsql',
            'dbname' => 'orm1',
            'server' => 'localhost',
            'user' => 'postgres',
            'password' => 'postgres',
        ]
    ],
];