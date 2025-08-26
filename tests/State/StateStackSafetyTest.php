<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\State;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\State\State;

final class StateStackSafetyTest extends TestCase
{
// tests/State/StateStackSafetyTest.php

    public function testLargeChainDoesNotOverflowStack(): void
    {
        $n = 10000;

        $prog = State::of(null);
        for ($i = 0; $i < $n; $i++) {
            $prog = $prog->flatMap(fn () => State::modify(fn (int $s): int => $s + 1));
        }

        [$s, $a] = $prog->run(0);
        $this->assertSame($n, $s);
        $this->assertNull($a);
    }

}
