<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\IO;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\IO\IO;
use PenguinPark\Monad\Either\Left;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Exception\UnwrapLeft;

final class IOFromEitherMessageTest extends TestCase
{
    public function testFromEitherRightReturnsValue(): void
    {
        $io = IO::fromEither(new Right(10));
        $this->assertSame(10, $io->unsafeRun());
    }

    public function testFromEitherLeftStringUsesStringDirectly(): void
    {
        $io = IO::fromEither(new Left('bad'));
        $this->expectException(UnwrapLeft::class);
        $this->expectExceptionMessage('bad');
        $io->unsafeRun();
    }

    public function testFromEitherLeftScalarIsCastedToString(): void
    {
        $io = IO::fromEither(new Left(5));
        $this->expectException(UnwrapLeft::class);
        $this->expectExceptionMessage('5');
        $io->unsafeRun();
    }

    public function testFromEitherLeftNonScalarFallsBackMessage(): void
    {
        $io = IO::fromEither(new Left(['x']));
        $this->expectException(UnwrapLeft::class);
        $this->expectExceptionMessage('Left error');
        $io->unsafeRun();
    }
}
