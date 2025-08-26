<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Exceptions;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\State\State;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Exception\ApCallableExpected;

final class StateExceptionsTest extends TestCase
{
    public function testFlatMapMustReturnState(): void
    {
        $this->expectException(InvalidBindReturnType::class);
        State::of(1)->flatMap(fn ($x) => 'nope')->run(0);
    }

    public function testApRequiresCallableHeldByReceiver(): void
    {
        $this->expectException(ApCallableExpected::class);
        State::of(123)->ap(State::of(1))->run(0);
    }
}
