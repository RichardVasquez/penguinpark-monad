<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;

final class ListFlatMapTest extends TestCase
{
    public function testFlatMapFlattens(): void
    {
        $xs = ListM::fromArray([1,2,3])
            ->flatMap(fn (int $x) => ListM::fromArray([$x, $x * 10]));
        $this->assertSame([1,10,2,20,3,30], $xs->toArray());
    }

    public function testFlatMapOnEmptyIsEmpty(): void
    {
        $xs = ListM::empty()->flatMap(fn ($x) => ListM::of($x));
        $this->assertTrue($xs->isEmpty());
    }
}
