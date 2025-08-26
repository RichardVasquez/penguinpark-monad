<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Exceptions;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\IO\IO;
use PenguinPark\Monad\Either\Left;
use PenguinPark\Monad\Maybe\Maybe;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\BracketReturnTypeExpected;
use PenguinPark\Monad\Exception\ContractViolation;
use PenguinPark\Monad\Exception\UnwrapLeft;
use PenguinPark\Monad\Exception\UnwrapNothing;
use RuntimeException;

final class IOExceptionsTest extends TestCase
{
    public function testFlatMapMustReturnIO(): void
    {
        $this->expectException(InvalidBindReturnType::class);
        IO::of(1)->flatMap(fn ($x) => 123)->unsafeRun();
    }

    public function testApRequiresCallableHeldByReceiver(): void
    {
        $this->expectException(ApCallableExpected::class);
        IO::of(123)->ap(IO::of(1))->unsafeRun();
    }

    public function testMapErrorMapperMustReturnThrowable(): void
    {
        $this->expectException(ContractViolation::class);
        IO::delay(fn () => throw new RuntimeException('x'))
            ->mapError(fn ($e) => 'not throwable')
            ->unsafeRun();
    }

    public function testHandleErrorWithMustReturnIO(): void
    {
        $this->expectException(ContractViolation::class);
        IO::delay(fn () => throw new RuntimeException('x'))
            ->handleErrorWith(fn ($e) => 'not io')
            ->unsafeRun();
    }

    public function testBracketUseMustReturnIO(): void
    {
        $this->expectException(BracketReturnTypeExpected::class);
        IO::bracket(
            IO::of('R'),
            fn ($r) => 'not io',
            fn ($r) => IO::of(null)
        )->unsafeRun();
    }

    public function testBracketReleaseMustReturnIO(): void
    {
        $this->expectException(BracketReturnTypeExpected::class);
        IO::bracket(
            IO::of('R'),
            fn ($r) => IO::of('ok'),
            fn ($r) => 'not io'
        )->unsafeRun();
    }

    public function testFromEitherLeftThrowsUnwrapLeft(): void
    {
        $this->expectException(UnwrapLeft::class);
        IO::fromEither(new Left('err'))->unsafeRun();
    }

    public function testFromMaybeNothingThrowsUnwrapNothing(): void
    {
        $this->expectException(UnwrapNothing::class);
        IO::fromMaybe(Maybe::nothing())->unsafeRun();
    }
}
