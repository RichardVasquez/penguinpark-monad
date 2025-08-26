<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\State;

use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\State\State;

final class StateFlatMapTypeGuardTest extends TestCase
{
    public function testFlatMapMustReturnState(): void
    {
        $prog = State::of(1)->flatMap(fn ($_) => /** @phpstan-ignore-next-line */ 'not-a-state');

        $this->expectException(InvalidBindReturnType::class);

        $prog->run(0);
    }
}
