<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Either\Left;

final class EitherFlatMapTest extends TestCase
{
    public function testFlatMapRightToRight(): void
    {
        $e = new Right(10)->flatMap(fn (int $x) => new Right($x * 2));
        $this->assertTrue($e->isRight());
        $this->assertSame(20, $e->getOrElse(-1));
    }

    public function testFlatMapRightToLeft(): void
    {
        $e = new Right(0)->flatMap(fn (int $x) => $x === 0 ? new Left('zero') : new Right($x));
        $this->assertTrue($e->isLeft());
        $this->assertSame('fallback', $e->getOrElse('fallback'));
    }

    public function testFlatMapOnLeftIsNoop(): void
    {
        $e = new Left('err')->flatMap(fn ($x) => new Right('nope'));
        $this->assertTrue($e->isLeft());
    }
}
