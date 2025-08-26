<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Either\Left;

final class EitherMapTest extends TestCase
{
    public function testMapOnRightTransforms(): void
    {
        $e = new Right(2)->map(fn (int $x): int => $x + 1);
        $this->assertSame(3, $e->getOrElse(-1));
        $this->assertTrue($e->isRight());
    }

    public function testMapOnLeftIsNoop(): void
    {
        $e = new Left('err')->map(fn ($x) => 123);
        $this->assertTrue($e->isLeft());
        $this->assertSame('fallback', $e->getOrElse('fallback'));
    }

    public function testMapCanReturnNullWithoutChangingBranch(): void
    {
        $e = new Right('x')->map(fn ($s) => null);
        $this->assertTrue($e->isRight());
        $this->assertNull($e->getOrElse('nope'));
    }
}
