<?php declare(strict_types=1);

namespace Doctrine\DBAL\Driver\Swoole\Coroutine\PostgreSQL;

use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;
use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use Swoole\ConnectionPool;
use Swoole\Coroutine\PostgreSQL;

final class Driver extends AbstractPostgreSQLDriver
{
    public const DEFAULT_POOL_SIZE = 8;
    private static ConnectionPool $pool;

    public function connect(array $params): ConnectionInterface
    {
        if (!isset(self::$pool)) {
            self::$pool = new ConnectionPool(
                fn(): Connection => $this->create($this->dsn($params)),
                $params['poolSize'] ?? self::DEFAULT_POOL_SIZE,
            );
        }

        $connection = self::$pool->get();
        defer(static fn() => self::$pool->put($connection));
        return $connection;
    }

    /**
     * @throws ConnectionException
     */
    public function create(string $dsn): Connection
    {
        $pgsql = new PostgreSQL();

        if (!$pgsql->connect($dsn)) {
            throw ConnectionException::failed($dsn);
        }

        return new Connection($pgsql);
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