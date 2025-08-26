<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;

final class ListFoldTest extends TestCase
{
    public function testFoldLeftSummation(): void
    {
        $sum = ListM::fromArray([1,2,3])->foldLeft(0, static fn (int $acc, int $x) => $acc + $x);
        $this->assertSame(6, $sum);
    }

    public function testFoldRightBuildsStringRightAssociative(): void
    {
        $s = ListM::fromArray(['a','b','c'])->foldRight('', static fn (string $x, string $acc) => $x . '(' . $acc . ')');
        $this->assertSame('a(b(c()))', $s);
    }
}
