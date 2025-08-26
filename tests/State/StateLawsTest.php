<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\State;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\State\State;

final class StateLawsTest extends TestCase
{
    public function testFunctorIdentityAndComposition(): void
    {
        $m = State::gets(fn (int $s) => $s + 1);
        $f = static fn (int $x): int => $x * 2;
        $g = static fn (int $x): int => $x + 3;
        $fg = static fn (int $x): int => $f($g($x));

        $env = 10;

        // identity
        $this->assertSame(
            $m->run($env),
            $m->map(static fn ($x) => $x)->run($env)
        );

        // composition
        $left  = $m->map($fg)->run($env);
        $right = $m->map($g)->map($f)->run($env);
        $this->assertSame($left, $right);
    }

    public function testMonadLeftRightIdentityAndAssociativity(): void
    {
        $env = 2;

        $f = static fn (int $a): State => State::of($a + 10);
        $g = static fn (int $a): State => State::of($a * 3);

        // Left identity: of(a) >>= f == f(a)
        $a = 7;
        $this->assertSame(
            State::of($a)->flatMap($f)->run($env),
            $f($a)->run($env)
        );

        // Right identity: m >>= of == m
        $m = State::gets(fn (int $s) => $s + 1);
        $this->assertSame(
            $m->flatMap([State::class, 'of'])->run($env),
            $m->run($env)
        );

        // Associativity
        $left  = $m->flatMap($f)->flatMap($g)->run($env);
        $right = $m->flatMap(fn ($x) => $f($x)->flatMap($g))->run($env);
        $this->assertSame($left, $right);
    }

    public function testGetPutModifyIdentities(): void
    {
        // get >>= put  == unit()
        $p1 = State::get()->flatMap(fn ($s) => State::put($s));
        $this->assertSame([5, null], $p1->run(5));

        // put(s) >> get  == returns s
        $p2 = State::put(42)->flatMap(fn () => State::get());
        $this->assertSame([42, 42], $p2->run(0));

        // modify then get reflects change
        $p3 = State::modify(fn (int $s) => $s + 1)->flatMap(fn () => State::get());
        $this->assertSame([11, 11], $p3->run(10));
    }
}
