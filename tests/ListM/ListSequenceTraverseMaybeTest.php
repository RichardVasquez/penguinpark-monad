<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;
use PenguinPark\Monad\Maybe\Maybe;

final class ListSequenceTraverseMaybeTest extends TestCase
{
    public function testSequenceMaybeAllJust(): void
    {
        $xs = ListM::fromArray([Maybe::just(1), Maybe::just(2), Maybe::just(3)]);
        $out = $xs->sequenceMaybe();

        $this->assertTrue($out->isJust());
        $this->assertSame([1,2,3], $out->getOrElse(ListM::empty())->toArray());
    }

    public function testSequenceMaybeWithNothingShortCircuits(): void
    {
        $xs = ListM::fromArray([Maybe::just(1), Maybe::nothing(), Maybe::just(3)]);
        $out = $xs->sequenceMaybe();

        $this->assertTrue($out->isNothing());
    }

    public function testTraverseMaybeSuccess(): void
    {
        $f = static fn (int $x) => $x > 0 ? Maybe::just($x * 10) : Maybe::nothing();

        $xs = ListM::fromArray([1,2,3]);
        $out = $xs->traverseMaybe($f);

        $this->assertTrue($out->isJust());
        $this->assertSame([10,20,30], $out->getOrElse(ListM::empty())->toArray());
    }

    public function testTraverseMaybeStopsAtFirstNothing(): void
    {
        $calls = 0;
        $f = function (int $x) use (&$calls) {
            $calls++;
            return $x === 2 ? Maybe::nothing() : Maybe::just($x);
        };

        $xs = ListM::fromArray([1,2,999]); // 999 should never be processed
        $out = $xs->traverseMaybe($f);

        $this->assertTrue($out->isNothing());
        $this->assertSame(2, $calls, 'should stop after first Nothing');
    }
}
