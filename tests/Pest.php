<?php declare(strict_types=1);

namespace Tests;

use Doctrine\DBAL\{Connection, Driver, DriverManager};

function conn(): Connection
{
    $params = [
        'dbname' => 'postgres',
        'user' => 'postgres',
        'password' => 'postgres',
        'host' => 'db',
        'driverClass' => Driver\Swoole\Coroutine\PostgreSQL\Driver::class
    ];

    return DriverManager::getConnection($params);
}