<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Util;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Either\Left;
use stdClass;

final class EitherEqualsExtraTest extends TestCase
{
    public function testRightFloatEqualityAndInequality(): void
    {
        $this->assertTrue(new Right(1.5)->equals(new Right(1.5)));
        $this->assertFalse(new Right(1.5)->equals(new Right(2.5)));
        $this->assertFalse(new Right(NAN)->equals(new Right(1.0)));
        $this->assertFalse(new Right(1.0)->equals(new Right(NAN)));
    }

    public function testArrayLengthMismatchFalse(): void
    {
        $this->assertFalse(new Right([1,2])->equals(new Right([1,2,3])));
    }

    public function testMixedTypesInequalityAndObjectIdentity(): void
    {
        $this->assertFalse(new Right([])->equals(new Right(new stdClass())));
        $o1 = new stdClass(); $o1->x = 1;
        $o2 = new stdClass(); $o2->x = 1;

        $this->assertTrue(new Right($o1)->equals(new Right($o1)));
        $this->assertFalse(new Right($o1)->equals(new Right($o2)));
    }

    public function testLeftBranchIndependence(): void
    {
        $this->assertFalse(new Left('e')->equals(new Right('e')));
    }
}
