<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Reader;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Reader\Reader;
use PenguinPark\Monad\IO\IO;

final class ReaderIOInteropTest extends TestCase
{
    public function testReaderProducingIOIsLazyUntilRunAndUnsafeRun(): void
    {
        $calls = 0;

        // Reader<array, IO<int>>
        $prog = Reader::ask()->map(function (array $env) use (&$calls) {
            return IO::delay(function () use (&$calls, $env) {
                $calls++;
                return ($env['x'] ?? 0) + 1;
            });
        });

        $env = ['x' => 10];

        $this->assertSame(0, $calls, 'building the Reader must not run the IO');
        $io = $prog->run($env);                // still 0
        $this->assertSame(0, $calls, 'running Reader returns IO without executing it');
        $this->assertSame(11, $io->unsafeRun());
        $this->assertSame(1, $calls, 'effect runs exactly once');
    }

    public function testLocalChangesEnvForProducedIO(): void
    {
        $calls = 0;

        $prog = Reader::ask()->map(function (array $env) use (&$calls) {
            return IO::delay(function () use (&$calls, $env) {
                $calls++;
                return ($env['x'] ?? 0) + 1;
            });
        });

        $env = ['x' => 10];
        $faster = $prog->local(fn (array $e) => [...$e, 'x' => 1]);

        $this->assertSame(2, $faster->run($env)->unsafeRun());
        $this->assertSame(11, $prog->run($env)->unsafeRun());
        $this->assertSame(2, $calls);
    }
}
