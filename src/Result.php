<?php declare(strict_types=1);

namespace Doctrine\DBAL\Driver\Swoole\Coroutine\PostgreSQL;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Result as ResultInterface;
use Swoole\Coroutine\PostgreSQL;

class Result implements ResultInterface
{
    private PostgreSQL $connection;
    private $result;

    public function __construct(PostgreSQL $connection, $result)
    {
        if (!is_resource($result)) {
            throw new \Doctrine\DBAL\Exception('Result must be a resource');
        }

        $this->connection = $connection;
        $this->result = $result;
    }

    public function fetchNumeric()
    {
        return $this->connection->fetchArray($this->result);
    }

    public function fetchAssociative()
    {
        return $this->connection->fetchAssoc($this->result);
    }

    public function fetchOne()
    {
        return $this->connection->fetchRow($this->result)[0];
    }

    public function fetchAllNumeric(): array
    {
        return $this->fetchAll('fetchNumeric');
    }

    public function fetchAllAssociative(): array
    {
        return $this->fetchAll('fetchAssociative');
    }

    public function fetchFirstColumn(): array
    {
        return array_column($this->fetchAll('fetchNumeric'), 0);
    }

    public function rowCount(): int
    {
        return $this->connection->numRows($this->result);
    }

    public function columnCount(): int
    {
        return $this->connection->fieldCount($this->result);
    }

    public function free(): void
    {
        $this->result = null;
    }

    private function fetchAll(string $method): array
    {
        $result_set = [];

        while ($row = [$this, $method]) {
            $result_set[] = $row;
        }

        return $result_set;
    }
}
