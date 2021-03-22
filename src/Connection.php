<?php declare(strict_types=1);

namespace Doctrine\DBAL\Driver\Swoole\Coroutine\PostgreSQL;

use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\ParameterType;
use Swoole\Coroutine\PostgreSQL;

final class Connection implements ConnectionInterface
{
    private PostgreSQL $connection;

    public function __construct(string $dsn)
    {
        $this->connection = new PostgreSQL();
        $this->connection->connect($dsn);
    }

    public function prepare(string $sql): Statement
    {
        return new Statement($this->connection, $sql);
    }

    public function query(string $sql): Result
    {
        return new Result($this->connection, $this->connection->query($sql));
    }

    public function quote($value, $type = ParameterType::STRING)
    {
        return "'" . $this->connection->escape($value) . "'";
    }

    public function exec(string $sql): int
    {
        return $this->connection->affectedRows($this->connection->query($sql));
    }

    public function lastInsertId($name = null)
    {
        return $this->query("SELECT CURRVAL('$name')")->fetchOne();
    }

    public function beginTransaction()
    {
        $this->connection->query('START TRANSACTION');
    }

    public function commit()
    {
        $this->connection->query('COMMIT');
    }

    public function rollBack()
    {
        $this->connection->query('ROLLBACK');
    }
}