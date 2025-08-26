<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;

final class ListFilterTest extends TestCase
{
    public function testFilterKeepsMatching(): void
    {
        $xs = ListM::fromArray([1,2,3,4])->filter(static fn (int $x) => $x % 2 === 0);
        $this->assertSame([2,4], $xs->toArray());
    }

    public function testFilterOnEmptyIsEmpty(): void
    {
        $this->assertTrue(ListM::empty()->filter(fn () => true)->isEmpty());
    }
}
