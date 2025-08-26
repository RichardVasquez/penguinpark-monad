<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;

final class ListConcatTest extends TestCase
{
    public function testAppendAndConcat(): void
    {
        $a = ListM::fromArray([1,2]);
        $b = ListM::fromArray([3]);
        $c = ListM::fromArray([4,5]);

        $this->assertSame([1,2,3], $a->append($b)->toArray());

        $all = $a->concat([$b, $c]);
        $this->assertSame([1,2,3,4,5], $all->toArray());
    }

    public function testConcatWithEmpties(): void
    {
        $a = ListM::fromArray([1]);
        $out = $a->concat([ListM::empty(), ListM::fromArray([]), ListM::fromArray([2])]);
        $this->assertSame([1,2], $out->toArray());
    }
}
