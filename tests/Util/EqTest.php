<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Util;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Util\Eq;
use stdClass;

final class EqTest extends TestCase
{
    public function testFloatNaNAndSignedZero(): void
    {
        $this->assertTrue(Eq::valueEq(NAN, NAN), 'NaN must equal NaN');
        $this->assertTrue(Eq::valueEq(+0.0, -0.0), '+0.0 must equal -0.0');
    }

    public function testFloatInequality(): void
    {
        $this->assertFalse(Eq::valueEq(1.5, 2.5));
    }

    public function testArrayLengthMismatchIsFalse(): void
    {
        $this->assertFalse(Eq::valueEq(['a' => 1], ['a' => 1, 'b' => 2]));
        $this->assertFalse(Eq::valueEq([1, 2], [1, 2, 3]));
    }

    public function testNestedArrayStructuralEquality(): void
    {
        $this->assertTrue(Eq::valueEq(['x' => [1,2]], ['y' => [1,2]]));
        $this->assertFalse(Eq::valueEq(['x' => [1,2]], ['y' => [2,1]]));
    }

    public function testMixedTypesInequality(): void
    {
        $this->assertFalse(Eq::valueEq(1, '1'));
        $this->assertFalse(Eq::valueEq([], (object)[]));
    }

    public function testObjectIdentityOnly(): void
    {
        $o1 = new stdClass(); $o1->x = 1;
        $o2 = new stdClass(); $o2->x = 1;

        $this->assertTrue(Eq::valueEq($o1, $o1), 'same instance should be equal');
        $this->assertFalse(Eq::valueEq($o1, $o2), 'different instances not equal by default');
    }
}
