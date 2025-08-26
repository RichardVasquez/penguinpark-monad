<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Maybe;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Maybe;

final class MaybeIntrospectionTest extends TestCase
{
    public function testIsJustIsNothingAndOrNull(): void
    {
        $j = Maybe::just(42);
        $n = Maybe::nothing();

        $this->assertTrue($j->isJust());
        $this->assertFalse($j->isNothing());
        $this->assertSame(42, $j->orNull());

        $this->assertTrue($n->isNothing());
        $this->assertFalse($n->isJust());
        $this->assertNull($n->orNull());
    }

    public function testOrElseSuppliesOnlyOnNothing(): void
    {
        $calls = 0;
        $supplier = function () use (&$calls) { $calls++; return Maybe::just('supplied'); };

        $a = Maybe::just('keep')->orElse($supplier);
        $b = Maybe::nothing()->orElse($supplier);

        $this->assertSame('keep', $a->getOrElse('x'));
        $this->assertSame('supplied', $b->getOrElse('x'));
        $this->assertSame(1, $calls);
    }
}
