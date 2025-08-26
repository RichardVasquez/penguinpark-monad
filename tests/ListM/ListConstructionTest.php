<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;

final class ListConstructionTest extends TestCase
{
    public function testFromArrayReindexesKeys(): void
    {
        $in  = [10 => 'a', 20 => 'b', 30 => 'c'];
        $xs  = ListM::fromArray($in)->toArray();

        // Keys must be 0..n-1 in order
        $this->assertSame([0,1,2], array_keys($xs));
        $this->assertSame(['a','b','c'], array_values($xs));
    }

    public function testOfPreservesOrder(): void
    {
        $xs = ListM::of('x', 'y', 'z')->toArray();
        $this->assertSame(['x','y','z'], $xs);
    }
}
