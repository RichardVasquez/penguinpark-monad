<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Maybe;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Maybe;

final class MaybeLawsTest extends TestCase
{
    public function testFunctorIdentity(): void
    {
        $id = static fn ($x) => $x;

        $this->assertSame(
            5,
            Maybe::just(5)->map($id)->getOrElse(-1)
        );
        $this->assertTrue(
            Maybe::nothing()->map($id)->isNothing()
        );
    }

    public function testFunctorComposition(): void
    {
        $f = static fn (int $x): int => $x * 2;
        $g = static fn (int $x): int => $x + 1;

        $left  = Maybe::just(3)->map(fn ($x) => $f($g($x)));
        $right = Maybe::just(3)->map($g)->map($f);

        $this->assertSame($left->getOrElse(-1), $right->getOrElse(-1));

        $this->assertTrue(
            Maybe::nothing()->map(fn ($x) => $f($g($x)))->isNothing()
        );
        $this->assertTrue(
            Maybe::nothing()->map($g)->map($f)->isNothing()
        );
    }

    public function testMonadLeftIdentity(): void
    {
        $f = static fn (int $x) => Maybe::just($x + 10);

        $left  = Maybe::of(5)->flatMap($f);
        $right = $f(5);

        $this->assertSame($right->getOrElse(-1), $left->getOrElse(-1));
    }

    public function testMonadRightIdentity(): void
    {
        $m = Maybe::just(7);
        $this->assertSame(
            $m->getOrElse(-1),
            $m->flatMap(fn ($x) => Maybe::of($x))->getOrElse(-1)
        );

        $n = Maybe::nothing();
        $this->assertTrue(
            $n->flatMap(fn ($x) => Maybe::of($x))->isNothing()
        );
    }

    public function testMonadAssociativity(): void
    {
        $f = static fn (int $x) => Maybe::just($x + 1);
        $g = static fn (int $x) => Maybe::just($x * 3);

        $m = Maybe::just(2);

        $left  = $m->flatMap($f)->flatMap($g);
        $right = $m->flatMap(fn ($x) => $f($x)->flatMap($g));

        $this->assertSame($left->getOrElse(-1), $right->getOrElse(-1));

        $n = Maybe::nothing();
        $this->assertTrue(
            $n->flatMap($f)->flatMap($g)->isNothing()
        );
        $this->assertTrue(
            $n->flatMap(fn ($x) => $f($x)->flatMap($g))->isNothing()
        );
    }
}
