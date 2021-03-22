# Swoole Coroutine PostgreSQL Doctrine DBAL Driver

🔌 A `Doctrine\DBAL\Driver` implementation on top of `Swoole\Coroutine\PostgreSQL`.

## Guides

### Use Composer through Docker

```shell
docker-compose run --rm composer install
docker-compose run --rm composer test
```

It will build a development image with PHP, Swoole, Swoole's PostgreSQL extension and PCOV for coverage.