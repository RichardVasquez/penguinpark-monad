<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Reader;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Reader\Reader;

final class ReaderLawsTest extends TestCase
{
    private function id(mixed $x): mixed { return $x; }

    public function testFunctorIdentityAndComposition(): void
    {
        $r  = Reader::ask()->map(fn (array $e) => $e['x'] ?? 0);
        $f  = static fn (int $n): int => $n + 1;
        $g  = static fn (int $n): int => $n * 2;
        $fg = static fn (int $n): int => $f($g($n));

        $env = ['x' => 5];

        // identity
        $this->assertSame($r->run($env), $r->map(static fn ($x) => $x)->run($env));

        // composition
        $left  = $r->map($fg)->run($env);
        $right = $r->map($g)->map($f)->run($env);
        $this->assertSame($left, $right);
    }

    public function testMonadLeftRightIdentityAndAssociativity(): void
    {
        $env = ['x' => 3];

        $f = static fn (int $a): Reader => Reader::of($a + 10); // Reader<E,int>
        $g = static fn (int $a): Reader => Reader::of($a * 2);

        // Left identity: of(a) >>= f == f(a)
        $a = 7;
        $this->assertSame(
            Reader::of($a)->flatMap($f)->run($env),
            $f($a)->run($env)
        );

        // Right identity: m >>= of == m
        $m = Reader::ask()->map(fn (array $e) => ($e['x'] ?? 0) + 1);
        $this->assertSame(
            $m->flatMap([Reader::class, 'of'])->run($env),
            $m->run($env)
        );

        // Associativity: (m >>= f) >>= g == m >>= (a => f(a) >>= g)
        $left  = $m->flatMap($f)->flatMap($g)->run($env);
        $right = $m->flatMap(fn ($a) => $f($a)->flatMap($g))->run($env);
        $this->assertSame($left, $right);
    }
}
