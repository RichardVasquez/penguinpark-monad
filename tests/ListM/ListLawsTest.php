<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;

final class ListLawsTest extends TestCase
{
    public function testMapDistributesOverAppend(): void
    {
        $f = static fn (int $x): int => $x + 1;
        $xs = ListM::fromArray([1,2]);
        $ys = ListM::fromArray([3]);

        $left  = $xs->append($ys)->map($f);
        $right = $xs->map($f)->append($ys->map($f));

        $this->assertSame($left->toArray(), $right->toArray());
    }

    public function testFlatMapEqualsMapThenConcat(): void
    {
        $f = fn (int $x) => ListM::fromArray([$x, $x + 10]);
        $xs = ListM::fromArray([1,2,3]);

        $left = $xs->flatMap($f);

        $mapped = $xs->map($f);
        $right = ListM::empty()->concat($mapped); // concat all sublists

        $this->assertSame($left->toArray(), $right->toArray());
    }
}
