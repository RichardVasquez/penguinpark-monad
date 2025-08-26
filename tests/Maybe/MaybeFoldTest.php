<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Maybe;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Maybe;
use RuntimeException;

final class MaybeFoldTest extends TestCase
{
    public function testFoldJustPath(): void
    {
        $m = Maybe::just('ok');
        $out = $m->fold(fn () => 'none', fn ($v) => strtoupper($v));
        $this->assertSame('OK', $out);
    }

    public function testFoldNothingPath(): void
    {
        $m = Maybe::nothing();
        $out = $m->fold(fn () => 'none', fn ($v) => strtoupper($v));
        $this->assertSame('none', $out);
    }

    public function testGetOrElseValueVsCallableLaziness(): void
    {
        $called = 0;
        $supplier = function () use (&$called) { $called++; return 'fallback'; };

        $a = Maybe::just('x')->getOrElse($supplier);
        $b = Maybe::nothing()->getOrElse($supplier);

        $this->assertSame('x', $a);
        $this->assertSame('fallback', $b);
        $this->assertSame(1, $called, 'supplier must be lazy and called exactly once');
    }

    public function testGetOrThrow(): void
    {
        $this->assertSame(1, Maybe::just(1)->getOrThrow('should not throw'));

        $this->expectException(RuntimeException::class);
        Maybe::nothing()->getOrThrow('boom');
    }
}
