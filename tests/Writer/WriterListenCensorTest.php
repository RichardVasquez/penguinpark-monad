<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Writer;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Writer\Writer;

final class WriterListenCensorTest extends TestCase
{
    public function testListenExposesCurrentLog(): void
    {
        $prog = Writer::tell('a')
            ->flatMap(fn () => Writer::of(10))
            ->listen();

        [$log, $pair] = $prog->run();
        $this->assertSame(['a'], $log);
        $this->assertSame([10, ['a']], $pair);
    }

    public function testCensorTransformsOnlyInnerLog(): void
    {
        $inner = Writer::tell('a')
            ->flatMap(fn () => Writer::tell('b'))
            ->map(fn () => 'inner-ok')
            ->censor(fn (array $log) => array_map('strtoupper', $log));

        $prog = Writer::of(null)
            ->flatMap(fn () => $inner)
            ->flatMap(fn () => Writer::tell('c'))
            ->map(fn () => 'outer-ok');

        [$log, $val] = $prog->run();

        // Only inner was uppercased; outer 'c' remains lowercase
        $this->assertSame(['A','B','c'], $log);
        $this->assertSame('outer-ok', $val);
    }
}