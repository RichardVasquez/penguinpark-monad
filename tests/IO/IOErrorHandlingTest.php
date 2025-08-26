<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\IO;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\IO\IO;
use PenguinPark\Monad\Exception\ContractViolation;
use InvalidArgumentException;
use RuntimeException;

final class IOErrorHandlingTest extends TestCase
{
    public function testMapErrorSuccessPathPreservesValue(): void
    {
        $io = IO::delay(fn () => 123)
            ->mapError(fn (\Throwable $e) => new InvalidArgumentException('mapped'));

        $this->assertSame(123, $io->unsafeRun(), 'mapError must not discard success value');
    }

    public function testMapErrorMapsAndRethrowsThrowable(): void
    {
        $io = IO::delay(function () { throw new RuntimeException('boom'); })
            ->mapError(fn (\Throwable $e) => new InvalidArgumentException('mapped'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('mapped');
        $io->unsafeRun();
    }

    public function testHandleErrorWithSuccessPathPreservesValue(): void
    {
        $io = IO::delay(fn () => 7)
            ->handleErrorWith(fn (\Throwable $e) => IO::of(99));
        $this->assertSame(7, $io->unsafeRun(), 'handleErrorWith must not discard success value');
    }

    public function testHandleErrorWithHandlesAndReturnsFallback(): void
    {
        $io = IO::delay(function () { throw new RuntimeException('x'); })
            ->handleErrorWith(fn (\Throwable $e) => IO::of(99));

        $this->assertSame(99, $io->unsafeRun());
    }

    public function testHandleErrorWithRequiresIO(): void
    {
        $io = IO::delay(function () { throw new RuntimeException('x'); })
            ->handleErrorWith(fn (\Throwable $e) => 111); // not an IO

        $this->expectException(ContractViolation::class);
        $io->unsafeRun();
    }
}
