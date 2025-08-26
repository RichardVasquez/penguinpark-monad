<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\ListM;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;

final class ListHeadLastTest extends TestCase
{
    public function testHeadAndLastOptions(): void
    {
        $xs = ListM::fromArray([10, 20, 30]);
        $this->assertSame(10, $xs->headOption()->getOrElse(-1));
        $this->assertSame(30, $xs->lastOption()->getOrElse(-1));
    }

    public function testHeadLastOnEmpty(): void
    {
        $xs = ListM::empty();
        $this->assertTrue($xs->headOption()->isNothing());
        $this->assertTrue($xs->lastOption()->isNothing());
    }
}
