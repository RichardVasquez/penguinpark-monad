<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Applicative;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;

final class ListMApplicativeTest extends TestCase
{
    private function id(mixed $x): mixed { return $x; }

    public function testIdentityLaw(): void
    {
        $v  = ListM::fromArray([1,2,3]);
        $id = static fn ($x) => $x;

        $out = ListM::pure($id)->ap($v);
        $this->assertTrue($out->equals($v));
    }

    public function testHomomorphismLaw(): void
    {
        $f = static fn (int $x): int => $x + 1;
        $x = 7;

        $left  = ListM::pure($f)->ap(ListM::pure($x));
        $right = ListM::pure($f($x));

        $this->assertTrue($left->equals($right));
    }

    public function testInterchangeLaw(): void
    {
        $y = 10;
        $u = ListM::pure(static fn (int $x): int => $x * 2);

        $left  = $u->ap(ListM::pure($y));
        $right = ListM::pure(static fn ($f) => $f($y))->ap($u);

        $this->assertTrue($left->equals($right));
    }

    public function testCompositionLaw(): void
    {
        $u = ListM::fromArray([static fn (int $x): int => $x + 1]);
        $v = ListM::fromArray([static fn (int $x): int => $x * 2]);
        $w = ListM::fromArray([3]);

        $compose = fn ($f) => fn ($g) => fn ($x) => $f($g($x));

        $left  = ListM::pure($compose)->ap($u)->ap($v)->ap($w);
        $right = $u->ap($v->ap($w));

        $this->assertTrue($left->equals($right));
    }

    public function testCartesianAp(): void
    {
        $fns = ListM::fromArray([
            static fn (int $x): int => $x + 1,
            static fn (int $x): int => $x * 10,
        ]);
        $xs  = ListM::fromArray([1,2]);

        $out = $fns->ap($xs)->toArray();
        $this->assertSame([2,3,10,20], $out);
    }

    public function testLiftA2IsCartesian(): void
    {
        $plus = static fn (int $a, int $b): int => $a + $b;

        $xs = ListM::fromArray([1,2]);
        $ys = ListM::fromArray([10,20]);

        $out = ListM::liftA2($plus, $xs, $ys)->toArray();
        $this->assertSame([11,21,12,22], $out);
    }

    public function testZipWithPairwise(): void
    {
        $xs = ListM::fromArray([1,2,3]);
        $ys = ListM::fromArray([10,20]);

        $sum = static fn (int $a, int $b): int => $a + $b;

        $out = $xs->zipWith($sum, $ys)->toArray();
        $this->assertSame([11,22], $out); // min length wins
    }
}
