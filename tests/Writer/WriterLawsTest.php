<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Writer;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Writer\Writer;

final class WriterLawsTest extends TestCase
{
    public function testFunctorIdentityAndComposition(): void
    {
        $w = Writer::tell('a')->flatMap(fn () => Writer::of(5));

        $id = static fn ($x) => $x;
        $f  = static fn (int $x): int => $x + 1;
        $g  = static fn (int $x): int => $x * 2;
        $fg = static fn (int $x): int => $f($g($x));

        $left  = $w->map($fg)->run();
        $right = $w->map($g)->map($f)->run();

        $this->assertSame($left, $right);
        $this->assertSame($w->run(), $w->map($id)->run());
    }

    public function testMonadLeftRightIdentityAndAssociativity(): void
    {
        $f = static fn (int $a): Writer => Writer::tell('f')->map(fn () => $a + 10);
        $g = static fn (int $a): Writer => Writer::tell('g')->map(fn () => $a * 2);

        $a = 7;

        // Left identity
        $this->assertSame(
            Writer::of($a)->flatMap($f)->run(),
            $f($a)->run()
        );

        // Right identity
        $m = Writer::tell('m')->flatMap(fn () => Writer::of(3));
        $this->assertSame(
            $m->flatMap([Writer::class, 'of'])->run(),
            $m->run()
        );

        // Associativity
        $left  = $m->flatMap($f)->flatMap($g)->run();
        $right = $m->flatMap(fn ($x) => $f($x)->flatMap($g))->run();
        $this->assertSame($left, $right);
    }

    public function testMonoidIdentityAndOrder(): void
    {
        // empty log acts as identity, and order is preserved
        $w = Writer::of('val');
        [$logEmpty, $v] = $w->run();
        $this->assertSame([], $logEmpty);
        $this->assertSame('val', $v);

        [$logOrder, ] = Writer::tell('a')
            ->flatMap(fn () => Writer::tell('b'))
            ->flatMap(fn () => Writer::tell('c'))
            ->run();

        $this->assertSame(['a','b','c'], $logOrder);
    }
}