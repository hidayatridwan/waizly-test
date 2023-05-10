<?php

function getDatabaseConfig(): array
{
    return [
        'database' => [
            'prod' => [
                'url' => 'mysql:host=localhost:3306;dbname=waizly',
                'username' => 'root',
                'password' => ''
            ],
            'test' => [
                'url' => 'mysql:host=localhost:3306;dbname=waizly_test',
                'username' => 'root',
                'password' => ''
            ]
        ]
    ];
}