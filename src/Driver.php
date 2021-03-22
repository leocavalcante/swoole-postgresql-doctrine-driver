<?php declare(strict_types=1);

namespace Doctrine\DBAL\Driver\Swoole\Coroutine\PostgreSQL;

use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;
use Doctrine\DBAL\Driver\Connection as ConnectionInterface;

final class Driver extends AbstractPostgreSQLDriver
{
    public function connect(array $params): ConnectionInterface
    {
        return new Connection($this->dsn($params));
    }

    private function dsn(array $params): string
    {
        if (array_key_exists('url', $params)) {
            return $params['url'];
        }

        $params['host'] ??= '127.0.0.1';
        $params['port'] ??= 5432;
        $params['dbname'] ??= 'postgres';
        $params['user'] ??= 'postgres';
        $params['password'] ??= 'postgres';

        return implode(';', [
            "host={$params['host']}",
            "port={$params['port']}",
            "dbname={$params['dbname']}",
            "user={$params['user']}",
            "password={$params['password']}",
        ]);
    }
}