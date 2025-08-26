<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Exceptions;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Maybe;
use PenguinPark\Monad\Maybe\Just;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\UnwrapNothing;

final class MaybeExceptionsTest extends TestCase
{
    public function testOrElseSupplierMustReturnMaybe(): void
    {
        $this->expectException(InvalidBindReturnType::class);
        Maybe::nothing()->orElse(fn () => 123);
    }

    public function testApRequiresCallableHeldByReceiver(): void
    {
        $this->expectException(ApCallableExpected::class);
        new Just(123)->ap(new Just(1));
    }

    public function testNothingGetThrowsUnwrapNothing(): void
    {
        $this->expectException(UnwrapNothing::class);
        Maybe::nothing()->get();
    }
}
