<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Maybe;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Maybe;

final class MaybeEqualsTest extends TestCase
{
    public function testReflexiveSymmetricTransitive(): void
    {
        $a = Maybe::just(42);
        $b = Maybe::just(42);
        $c = Maybe::just(42);

        $this->assertTrue($a->equals($a), 'reflexive');
        $this->assertTrue($a->equals($b) && $b->equals($a), 'symmetric');
        $this->assertTrue($a->equals($b) && $b->equals($c) && $a->equals($c), 'transitive');

        $n1 = Maybe::nothing();
        $n2 = Maybe::nothing();
        $this->assertTrue($n1->equals($n2));
    }

    public function testJustNullIsNotNothing(): void
    {
        $this->assertFalse(Maybe::just(null)->equals(Maybe::nothing()));
    }

    public function testDifferentValuesAreNotEqual(): void
    {
        $this->assertFalse(Maybe::just(1)->equals(Maybe::just(2)));
    }

    public function testFloatEdgesNanAndSignedZero(): void
    {
        $this->assertTrue(Maybe::just(NAN)->equals(Maybe::just(NAN)));
        $this->assertTrue(Maybe::just(+0.0)->equals(Maybe::just(-0.0)));
        $this->assertFalse(Maybe::just(1.0)->equals(Maybe::just(1.0000001)));
    }

    public function testArrayStructuralEquality(): void
    {
        $a = Maybe::just(['x' => 1, 'y' => [2,3]]);
        $b = Maybe::just(['x' => 1, 'y' => [2,3]]);
        $c = Maybe::just(['x' => 1, 'y' => [3,2]]);

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    public function testSubstitutivityUnderMap(): void
    {
        $a = Maybe::just(10);
        $b = Maybe::just(10);

        $this->assertTrue($a->equals($b));
        $f = static fn (int $x): int => $x * 3 + 1;

        $this->assertTrue($a->map($f)->equals($b->map($f)));
    }
}
