<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Right;

final class EitherMapRightTest extends TestCase
{
    public function testMapOnRightTransforms(): void
    {
        $e = new Right(2)->map(static fn(int $x): int => $x + 1);
        $this->assertTrue($e->isRight());
        $this->assertSame(3, $e->getOrElse(-1));
    }

    public function testMapOnRightCanReturnNull(): void
    {
        $e = new Right('x')->map(static fn(string $s) => null);
        $this->assertTrue($e->isRight());
        $this->assertNull($e->getOrElse('nope'));
    }

    public function testFunctorCompositionOnRight(): void
    {
        $f = static fn(int $x): int => $x * 2;
        $g = static fn(int $x): int => $x + 1;

        $left = new Right(3)->map(static fn($x) => $f($g($x)));
        $right = new Right(3)->map($g)->map($f);

        $this->assertSame($left->getOrElse(-1), $right->getOrElse(-1));
    }
}
