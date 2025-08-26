<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Either\Left;

final class ListSequenceTraverseEitherTest extends TestCase
{
    public function testSequenceEitherAllRight(): void
    {
        $xs = ListM::fromArray([new Right(1), new Right(2)]);
        $out = $xs->sequenceEither();

        $this->assertTrue($out->isRight());
        $this->assertSame([1,2], $out->getOrElse(ListM::empty())->toArray());
    }

    public function testSequenceEitherLeftShortCircuits(): void
    {
        $xs = ListM::fromArray([new Right(1), new Left('err'), new Right(3)]);
        $out = $xs->sequenceEither();

        $this->assertTrue($out->isLeft());
        $this->assertSame('err', $out->fold(fn ($l) => $l, fn ($r) => null));
    }

    public function testTraverseEitherSuccess(): void
    {
        $f = static fn (int $x) => $x % 2 === 0 ? new Right($x * 5) : new Left('odd');

        $xs = ListM::fromArray([2,4]);
        $out = $xs->traverseEither($f);

        $this->assertTrue($out->isRight());
        $this->assertSame([10,20], $out->getOrElse(ListM::empty())->toArray());
    }

    public function testTraverseEitherStopsAtFirstLeft(): void
    {
        $calls = 0;
        $f = function (int $x) use (&$calls) {
            $calls++;
            return $x === 3 ? new Left('boom') : new Right($x);
        };

        $xs = ListM::fromArray([1,3,999]);
        $out = $xs->traverseEither($f);

        $this->assertTrue($out->isLeft());
        $this->assertSame('boom', $out->fold(fn ($l) => $l, fn ($r) => null));
        $this->assertSame(2, $calls, 'should stop after first Left');
    }
}
