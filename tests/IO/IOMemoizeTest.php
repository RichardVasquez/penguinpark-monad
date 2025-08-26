<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\IO;

use PenguinPark\Monad\IO\IO;
use PHPUnit\Framework\TestCase;

final class IOMemoizeTest extends TestCase
{
    public function testMemoizePreventsReExecution(): void
    {
        $calls = 0;
        $io = IO::delay(function () use (&$calls) { $calls++; return 99; })->memoize();

        $this->assertSame(99, $io->unsafeRun());
        $this->assertSame(99, $io->unsafeRun());
        $this->assertSame(1, $calls);
    }
}
