<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;

final class ListEqualsTest extends TestCase
{
    public function testValueEqualitySameOrderSameValues(): void
    {
        $a = ListM::fromArray([1,2,3]);
        $b = ListM::fromArray([1,2,3]);

        $this->assertTrue($a->equals($b));
    }

    public function testDifferentOrderNotEqual(): void
    {
        $a = ListM::fromArray([1,2,3]);
        $b = ListM::fromArray([1,3,2]);

        $this->assertFalse($a->equals($b));
    }

    public function testDifferentLengthNotEqual(): void
    {
        $a = ListM::fromArray([1,2]);
        $b = ListM::fromArray([1,2,3]);

        $this->assertFalse($a->equals($b));
    }

    public function testNestedArraysStructural(): void
    {
        $a = ListM::fromArray([[1,2], [3,4]]);
        $b = ListM::fromArray([[1,2], [3,4]]);
        $c = ListM::fromArray([[1,2], [4,3]]);

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    public function testFloatEdgesNanAndSignedZero(): void
    {
        $this->assertTrue(ListM::fromArray([NAN])->equals(ListM::fromArray([NAN])));
        $this->assertTrue(ListM::fromArray([+0.0])->equals(ListM::fromArray([-0.0])));
    }

    public function testEquivalenceLaws(): void
    {
        $a = ListM::fromArray(['x','y']);
        $b = ListM::fromArray(['x','y']);
        $c = ListM::fromArray(['x','y']);

        $this->assertTrue($a->equals($a), 'reflexive');
        $this->assertTrue($a->equals($b) && $b->equals($a), 'symmetric');
        $this->assertTrue($a->equals($b) && $b->equals($c) && $a->equals($c), 'transitive');
    }

    public function testCongruenceUnderMap(): void
    {
        $a = ListM::fromArray([1,2,3]);
        $b = ListM::fromArray([1,2,3]);
        $f = static fn (int $x): int => $x * 7;

        $this->assertTrue($a->equals($b));
        $this->assertTrue($a->map($f)->equals($b->map($f)));
    }
}
