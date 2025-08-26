<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;
use PenguinPark\Monad\Either\Right;

final class ListSequenceEitherEdgeTest extends TestCase
{
    public function testSequenceEitherOnEmptyIsRightEmptyList(): void
    {
        $xs = ListM::empty();
        $out = $xs->sequenceEither();

        $this->assertTrue($out->isRight());
        $this->assertSame([], $out->getOrElse(ListM::fromArray([-1]))->toArray());
    }

    public function testSequenceEitherAllowsNullPayloads(): void
    {
        $xs = ListM::fromArray([new Right(null)]);
        $out = $xs->sequenceEither();

        $this->assertTrue($out->isRight());
        $this->assertSame([null], $out->getOrElse(ListM::empty())->toArray());
    }
}
