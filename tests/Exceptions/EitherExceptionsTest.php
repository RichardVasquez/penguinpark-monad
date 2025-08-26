<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Exceptions;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Left;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\UnwrapLeft;
use Throwable;

final class EitherExceptionsTest extends TestCase
{
    public function testRightFlatMapMustReturnEither(): void
    {
        $this->expectException(InvalidBindReturnType::class);
        new Right(1)->flatMap(fn ($x) => 123);
    }

    public function testApRequiresCallableHeldByReceiver(): void
    {
        $this->expectException(ApCallableExpected::class);
        new Right(123)->ap(new Right(1));
    }

    public function testLeftGetThrowsUnwrapLeft(): void
    {
        $this->expectException(UnwrapLeft::class);
        new Left('L')->get();
    }

    /**
     * @throws Throwable
     */
    public function testGetOrThrowMapperReturningNonScalarFallsBackToUnwrapLeftDefault(): void
    {
        $this->expectException(UnwrapLeft::class);
        $this->expectExceptionMessage('Left value');
        new Left('L')->getOrThrow(fn ($l) => ['not' => 'scalar']);
    }
}
