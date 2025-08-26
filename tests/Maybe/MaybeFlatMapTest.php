<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Maybe;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Maybe;

final class MaybeFlatMapTest extends TestCase
{
    public function testFlatMapJustToJust(): void
    {
        $m = Maybe::just(10)->flatMap(fn (int $x) => Maybe::just($x * 2));
        $this->assertTrue($m->isJust());
        $this->assertSame(20, $m->getOrElse(-1));
    }

    public function testFlatMapJustToNothing(): void
    {
        $m = Maybe::just(0)->flatMap(fn (int $x) => $x === 0 ? Maybe::nothing() : Maybe::just($x));
        $this->assertTrue($m->isNothing());
    }

    public function testFlatMapOnNothingIsNoop(): void
    {
        $m = Maybe::nothing()->flatMap(fn ($x) => Maybe::just('nope'));
        $this->assertTrue($m->isNothing());
    }
}
