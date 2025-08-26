<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Util;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Util\Eq;

final class EqFloatEdgeTest extends TestCase
{
    public function testNaNEqualsNaN(): void
    {
        $this->assertTrue(Eq::valueEq(NAN, NAN));
    }

    public function testNanComparedToNonFloatIsFalseAndDoesNotThrow(): void
    {
        // If the float gate is wrong (||), this would call is_nan() on an int and throw.
        $this->assertFalse(Eq::valueEq(NAN, 0));
        $this->assertFalse(Eq::valueEq(NAN, '0'));  // non-float other side
    }
}
