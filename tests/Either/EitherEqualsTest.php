<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Left;
use PenguinPark\Monad\Either\Right;

final class EitherEqualsTest extends TestCase
{
    public function testBranchMustMatch(): void
    {
        $this->assertFalse(new Left('e')->equals(new Right('e')));
    }

    public function testLeftStructuralEquality(): void
    {
        $a = new Left(['code' => 123, 'ctx' => ['a']]);
        $b = new Left(['code' => 123, 'ctx' => ['a']]);
        $c = new Left(['code' => 124, 'ctx' => ['a']]);

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    public function testRightStructuralEquality(): void
    {
        $a = new Right([1, 2, 3]);
        $b = new Right([1, 2, 3]);
        $c = new Right([1, 3, 2]);

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    public function testFloatEdgesNanAndSignedZero(): void
    {
        $this->assertTrue(new Right(NAN)->equals(new Right(NAN)));
        $this->assertTrue(new Right(+0.0)->equals(new Right(-0.0)));
    }

    public function testSubstitutivityRightBias(): void
    {
        $r1 = new Right(5);
        $r2 = new Right(5);
        $f = static fn(int $x): int => $x - 1;

        $this->assertTrue($r1->equals($r2));
        $this->assertTrue($r1->map($f)->equals($r2->map($f)));

        $l1 = new Left('e');
        $l2 = new Left('e');
        $this->assertTrue($l1->equals($l2));
        // map on Lefts is no-op; equality should hold
        $this->assertTrue($l1->map($f)->equals($l2->map($f)));
    }
}
