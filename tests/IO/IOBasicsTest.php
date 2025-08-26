<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\IO;

use PenguinPark\Monad\IO\IO;
use PHPUnit\Framework\TestCase;

final class IOBasicsTest extends TestCase
{
    public function testDoesNotExecuteUntilRun(): void
    {
        $calls = 0;
        $io = IO::delay(function () use (&$calls) {
            $calls++;
            return 42;
        })->map(fn (int $x) => $x + 1); // chain shouldn’t execute

        $this->assertSame(0, $calls);
        $this->assertSame(43, $io->unsafeRun());
        $this->assertSame(1, $calls);
    }

    public function testMapAndFlatMapCompose(): void
    {
        $io = IO::of(10)
            ->map(fn (int $x) => $x + 1)
            ->flatMap(fn (int $y) => IO::of($y * 2));

        $this->assertSame(22, $io->unsafeRun());
    }

    public function testTapRunsExactlyOnceDuringRun(): void
    {
        $seen = null;
        $io = IO::of(5)->tap(function ($v) use (&$seen) { $seen = $v; });

        $this->assertNull($seen);
        $this->assertSame(5, $io->unsafeRun());
        $this->assertSame(5, $seen);
    }
}
