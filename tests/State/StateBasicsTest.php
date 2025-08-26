<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\State;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\State\State;

final class StateBasicsTest extends TestCase
{
    public function testGetPutModifyGetsAndRun(): void
    {
        // get -> modify(+1) -> gets(double) -> result
        $prog = State::get()
            ->flatMap(fn (int $s) => State::modify(fn (int $x) => $x + 1))
            ->flatMap(fn () => State::gets(fn (int $s) => $s * 2));

        [$s1, $a] = $prog->run(10);
        $this->assertSame(11, $s1);  // state incremented
        $this->assertSame(22, $a);   // value is doubled new state

        // put overrides state, value is null
        [$s2, $a2] = State::put(5)->run(99);
        $this->assertSame(5, $s2);
        $this->assertNull($a2);

        // eval/exec helpers
        $this->assertSame(11, $prog->exec(10));
        $this->assertSame(22, $prog->eval(10));
    }

// tests/State/StateBasicsTest.php

    public function testBuildingIsLazyUntilRun(): void
    {
        $calls = 0;
        $inc = function (int $s) use (&$calls): int {
            $calls++;
            return $s + 1;
        };

        $prog = State::of(null)
            ->flatMap(fn () => State::modify($inc))
            ->flatMap(fn () => State::modify($inc));

        $this->assertSame(0, $calls, 'no evaluation during construction');
        [$s, $v] = $prog->run(0);
        $this->assertSame(2, $calls, 'closures run exactly on run()');
        $this->assertSame(2, $s);
        $this->assertNull($v);
    }


}
