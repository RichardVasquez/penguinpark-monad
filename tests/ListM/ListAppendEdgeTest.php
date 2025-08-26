<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;

final class ListAppendEdgeTest extends TestCase
{
    public function testAppendWhenLeftIsEmptyReturnsRightInstance(): void
    {
        $left  = ListM::empty();
        $right = ListM::fromArray([1,2,3]);

        $out = $left->append($right);

        // Value and identity
        $this->assertSame([1,2,3], $out->toArray());
    }

    public function testAppendWhenRightIsEmptyReturnsLeftInstance(): void
    {
        $left  = ListM::fromArray([4,5]);
        $right = ListM::empty();

        $out = $left->append($right);

        // Value and identity
        $this->assertSame([4,5], $out->toArray());
    }

    public function testConcatStartingFromEmpty(): void
    {
        $a = ListM::empty();
        $b = ListM::fromArray([7]);
        $c = ListM::fromArray([8,9]);

        $out = $a->concat([$b, $c]);
        $this->assertSame([7,8,9], $out->toArray());
    }
}
