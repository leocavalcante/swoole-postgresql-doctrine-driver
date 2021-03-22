<?php declare(strict_types=1);

namespace Tests;

use Swoole\Coroutine;

it('executes basic selects', function (): void {
    Coroutine\run(static function (): void {
        $actual = conn()->executeQuery('select 1+1 as total')->fetchOne();
        expect($actual)->toBe(2);
    });
});

it('executes concurrently when inside coroutines', function (): void {
    Coroutine\run(static function (): void {
        $sleep = static fn(string $test) => conn()->executeQuery("select '$test', pg_sleep(1)");

        $expected = ['foo', 'bar', 'baz'];
        $actual = [];

        $wg = new Coroutine\WaitGroup(count($expected));
        $start_time = time();

        foreach ($expected as $key => $test) {
            Coroutine::create(static function () use ($key, &$actual, $sleep, $test, $wg) {
                $actual[] = $sleep($test)->fetchOne();
                $wg->done();
            });
        }

        $wg->wait();
        $elapsed = time() - $start_time;


        expect($elapsed)->toBe(1);
        expect($actual)->toMatchArray($expected);
    });
});
