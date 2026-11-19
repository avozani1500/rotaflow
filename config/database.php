<?php

declare(strict_types=1);

/**
 * Configuração padrão para o MySQL/MariaDB do XAMPP.
 * Para outra máquina, copie database.example.php para database.local.php.
 */
return is_file(__DIR__ . '/database.local.php')
    ? require __DIR__ . '/database.local.php'
    : [
        'host' => getenv('ROTAFLOW_DB_HOST') ?: '127.0.0.1',
        'port' => getenv('ROTAFLOW_DB_PORT') ?: '3306',
        'database' => getenv('ROTAFLOW_DB_NAME') ?: 'rotaflow',
        'username' => getenv('ROTAFLOW_DB_USER') ?: 'root',
        'password' => getenv('ROTAFLOW_DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ];

