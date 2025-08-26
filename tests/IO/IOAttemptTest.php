<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\IO;

use PenguinPark\Monad\Either\Left;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\IO\IO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class IOAttemptTest extends TestCase
{
    public function testAttemptSuccessToRight(): void
    {
        $out = IO::of(7)->attempt()->unsafeRun();
        $this->assertInstanceOf(Right::class, $out);
        $this->assertSame(7, $out->getOrElse(-1));
    }

    public function testAttemptFailureToLeft(): void
    {
        $io = IO::delay(fn () => throw new RuntimeException('boom'));
        $e = $io->attempt()->unsafeRun();

        $this->assertInstanceOf(Left::class, $e);
        $this->assertSame('boom', $e->fold(fn ($l) => $l->getMessage(), fn () => 'nope'));
    }
}
