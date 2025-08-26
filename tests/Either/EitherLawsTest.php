<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Either\Left;

final class EitherLawsTest extends TestCase
{
    public function testFunctorIdentityRightBias(): void
    {
        $id = static fn ($x) => $x;

        $this->assertSame(
            5,
            new Right(5)->map($id)->getOrElse(-1)
        );
        $this->assertTrue(
            new Left('e')->map($id)->isLeft()
        );
    }

    public function testFunctorComposition(): void
    {
        $f = static fn (int $x): int => $x * 2;
        $g = static fn (int $x): int => $x + 1;

        $left  = new Right(3)->map(fn ($x) => $f($g($x)));
        $right = new Right(3)->map($g)->map($f);

        $this->assertSame($left->getOrElse(-1), $right->getOrElse(-1));

        $this->assertTrue(
            new Left('e')->map(fn ($x) => $f($g($x)))->isLeft()
        );
        $this->assertTrue(
            new Left('e')->map($g)->map($f)->isLeft()
        );
    }

    public function testMonadLawsRightBiased(): void
    {
        $unit = fn (int $x) => new Right($x);
        $f = fn (int $x) => new Right($x + 10);
        $g = fn (int $x) => new Right($x * 3);

        // Left identity
        $this->assertSame(
            $f(5)->getOrElse(-1),
            $unit(5)->flatMap($f)->getOrElse(-1)
        );

        // Right identity
        $m = new Right(7);
        $this->assertSame(
            $m->getOrElse(-1),
            $m->flatMap($unit)->getOrElse(-1)
        );

        // Associativity
        $m = new Right(2);
        $left  = $m->flatMap($f)->flatMap($g);
        $right = $m->flatMap(fn ($x) => $f($x)->flatMap($g));
        $this->assertSame($left->getOrElse(-1), $right->getOrElse(-1));

        // Left stays left through binds
        $n = new Left('e');
        $this->assertTrue($n->flatMap($f)->isLeft());
        $this->assertTrue($n->flatMap(fn ($x) => $f($x)->flatMap($g))->isLeft());
    }
}
