<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\IO;

use PenguinPark\Monad\IO\IO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class IOMemoizeFailureTest extends TestCase
{
    public function testMemoizeCachesFailureAndRethrows(): void
    {
        $calls = 0;
        $io = IO::delay(function () use (&$calls) {
            $calls++;
            throw new RuntimeException('nope');
        })->memoize();

        // First run throws
        try {
            $io->unsafeRun();
            $this->fail('Expected RuntimeException');
        } catch (RuntimeException $e) {
            $this->assertSame('nope', $e->getMessage());
        }
        $this->assertSame(1, $calls, 'thunk executed exactly once so far');

        // Second run must rethrow the cached exception without re-executing
        try {
            $io->unsafeRun();
            $this->fail('Expected RuntimeException');
        } catch (RuntimeException $e) {
            $this->assertSame('nope', $e->getMessage());
        }
        $this->assertSame(1, $calls, 'memoized failure must not re-execute the thunk');
    }
}
