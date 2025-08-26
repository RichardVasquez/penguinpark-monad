<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;

final class ListMapTest extends TestCase
{
    public function testMapTransformsEachElement(): void
    {
        $xs = ListM::fromArray([1,2,3])->map(static fn (int $x): int => $x + 1);
        $this->assertSame([2,3,4], $xs->toArray());
    }

    public function testMapCanReturnNulls(): void
    {
        $xs = ListM::fromArray(['a','b'])->map(static fn () => null);
        $this->assertSame([null, null], $xs->toArray());
    }
}
