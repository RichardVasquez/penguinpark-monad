<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Maybe;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Maybe;

final class MaybeFilterTest extends TestCase
{
    public function testFilterKeepsWhenPredicateTrue(): void
    {
        $m = Maybe::just(5)->filter(fn (int $x) => $x > 3);
        $this->assertTrue($m->isJust());
        $this->assertSame(5, $m->getOrElse(-1));
    }

    public function testFilterDropsWhenPredicateFalse(): void
    {
        $m = Maybe::just(2)->filter(fn (int $x) => $x > 3);
        $this->assertTrue($m->isNothing());
    }

    public function testFilterOnNothingIsNoop(): void
    {
        $m = Maybe::nothing()->filter(fn () => true);
        $this->assertTrue($m->isNothing());
    }
}
