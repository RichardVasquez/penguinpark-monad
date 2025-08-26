<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Applicative;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Maybe;

final class MaybeApplicativeTest extends TestCase
{
    private function id(mixed $x): mixed { return $x; }
    private function compose(callable $f, callable $g): callable {
        return static fn ($x) => $f($g($x));
    }

    public function testIdentityLaw(): void
    {
        $v  = Maybe::just(42);
        $id = static fn ($x) => $x;

        $this->assertTrue(
            Maybe::pure($id)->ap($v)->equals($v)
        );
    }

    public function testHomomorphismLaw(): void
    {
        $f = static fn (int $x): int => $x + 1;
        $x = 7;

        $left  = Maybe::pure($f)->ap(Maybe::pure($x));
        $right = Maybe::pure($f($x));

        $this->assertTrue($left->equals($right));
    }

    public function testInterchangeLaw(): void
    {
        $y = 10;
        $u = Maybe::pure(static fn (int $x): int => $x * 2);

        $left  = $u->ap(Maybe::pure($y));
        $right = Maybe::pure(static fn ($f) => $f($y))->ap($u);

        $this->assertTrue($left->equals($right));
    }

    public function testCompositionLaw(): void
    {
        $u = Maybe::pure(static fn (int $x): int => $x + 1);
        $v = Maybe::pure(static fn (int $x): int => $x * 2);
        $w = Maybe::pure(3);

        $compose = fn ($f) => fn ($g) => fn ($x) => $f($g($x));

        $left  = Maybe::pure($compose)->ap($u)->ap($v)->ap($w);
        $right = $u->ap($v->ap($w));

        $this->assertTrue($left->equals($right));
    }

    public function testShortCircuitWithNothing(): void
    {
        $f = Maybe::pure(static fn (int $x): int => $x + 1);

        $this->assertTrue(
            $f->ap(Maybe::nothing())->isNothing()
        );
        $this->assertTrue(
            Maybe::nothing()->ap(Maybe::pure(1))->isNothing()
        );
    }

    public function testLiftA2Convenience(): void
    {
        $mk = static fn (int $a, int $b): array => ['a' => $a, 'b' => $b];

        $ok  = Maybe::liftA2($mk, Maybe::just(1), Maybe::just(2));
        $nok = Maybe::liftA2($mk, Maybe::just(1), Maybe::nothing());

        $this->assertTrue($ok->equals(Maybe::just(['a'=>1,'b'=>2])));
        $this->assertTrue($nok->isNothing());
    }
}

