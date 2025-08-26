<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Applicative;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Either;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Either\Left;

final class EitherApplicativeTest extends TestCase
{
    private function id(mixed $x): mixed { return $x; }

    public function testIdentityLawRightBias(): void
    {
        $v  = new Right(42);
        $id = static fn ($x) => $x;

        $this->assertTrue(
            Either::pure($id)->ap($v)->equals($v)
        );
    }

    public function testHomomorphismLaw(): void
    {
        $f = static fn (int $x): int => $x + 1;
        $x = 7;

        $left  = Either::pure($f)->ap(Either::pure($x));
        $right = Either::pure($f($x));

        $this->assertTrue($left->equals($right));
    }

    public function testInterchangeLaw(): void
    {
        $y = 10;
        $u = Either::pure(static fn (int $x): int => $x * 2);

        $left  = $u->ap(Either::pure($y));
        $right = Either::pure(static fn ($f) => $f($y))->ap($u);

        $this->assertTrue($left->equals($right));
    }

    public function testCompositionLaw(): void
    {
        $u = Either::pure(static fn (int $x): int => $x + 1);
        $v = Either::pure(static fn (int $x): int => $x * 2);
        $w = Either::pure(3);

        $compose = fn ($f) => fn ($g) => fn ($x) => $f($g($x));

        $left  = Either::pure($compose)->ap($u)->ap($v)->ap($w);
        $right = $u->ap($v->ap($w));

        $this->assertTrue($left->equals($right));
    }

    public function testShortCircuitFirstLeftWins(): void
    {
        $f = Either::pure(static fn (int $x): int => $x + 1);

        $a = $f->ap(new Left('E1'));
        $b = new Left('E2')->ap(Either::pure(1));

        $this->assertTrue($a->equals(new Left('E1')));
        $this->assertTrue($b->equals(new Left('E2')));
    }

    public function testLiftA2RightBias(): void
    {
        $mk = static fn (int $a, int $b): array => ['a' => $a, 'b' => $b];

        $ok  = Either::liftA2($mk, new Right(1), new Right(2));
        $l1  = Either::liftA2($mk, new Left('e1'), new Right(2));
        $l2  = Either::liftA2($mk, new Right(1), new Left('e2'));

        $this->assertTrue($ok->equals(new Right(['a'=>1,'b'=>2])));
        $this->assertTrue($l1->equals(new Left('e1')));
        $this->assertTrue($l2->equals(new Left('e2')));
    }
}
