<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Writer;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Writer\Writer;

final class WriterBasicsTest extends TestCase
{
    public function testTellAndFlatMapOrderAndRun(): void
    {
        $w = Writer::tell('a')
            ->flatMap(fn () => Writer::tell('b'))
            ->flatMap(fn () => Writer::of(42));

        [$log, $val] = $w->run();

        $this->assertSame(['a','b'], $log);
        $this->assertSame(42, $val);
    }

    public function testMapDoesNotChangeLog(): void
    {
        [$log1, $val1] = Writer::of(2)->run();
        [$log2, $val2] = Writer::of(2)->map(fn (int $x) => $x * 3)->run();

        $this->assertSame([], $log1);
        $this->assertSame([], $log2);
        $this->assertSame(2, $val1);
        $this->assertSame(6, $val2);
    }

    public function testFlatMapAppendsLogsLeftToRight(): void
    {
        $prog = Writer::tell('x')
            ->flatMap(fn () => Writer::of(1))
            ->flatMap(fn (int $n) => Writer::tell('y')->map(fn () => $n + 1))
            ->flatMap(fn (int $n) => Writer::tell('z')->map(fn () => $n * 10));

        [$log, $val] = $prog->run();
        $this->assertSame(['x','y','z'], $log);
        $this->assertSame(20, $val);
    }
}