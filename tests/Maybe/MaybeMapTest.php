<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Maybe;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Maybe;

final class MaybeMapTest extends TestCase
{
    public function testMapOnJustTransformsValue(): void
    {
        $m = Maybe::just(2)->map(fn (int $x): int => $x + 1);
        $this->assertTrue($m->isJust());
        $this->assertSame(3, $m->getOrElse(-1));
    }

    public function testMapOnNothingIsNoop(): void
    {
        $m = Maybe::nothing()->map(fn ($x) => 123);
        $this->assertTrue($m->isNothing());
        $this->assertNull($m->orNull());
    }

    public function testMapCanReturnNullWithoutBecomingNothing(): void
    {
        $m = Maybe::just('x')->map(fn ($s) => null);
        $this->assertTrue($m->isJust());
        $this->assertNull($m->getOrElse('fallback'));
    }
}
