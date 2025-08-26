<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Either\Left;

final class EitherFoldTest extends TestCase
{
    public function testFoldChoosesBranch(): void
    {
        $l = new Left('err')->fold(fn ($l) => "L:$l", fn ($r) => "R:$r");
        $r = new Right(7)->fold(fn ($l) => "L:$l", fn ($r) => "R:$r");

        $this->assertSame('L:err', $l);
        $this->assertSame('R:7', $r);
    }

    public function testGetOrElseAndOrElseLaziness(): void
    {
        $calls = 0;
        $supplier = function () use (&$calls) { $calls++; return 99; };

        $a = new Right(1)->getOrElse($supplier);
        $b = new Left('e')->getOrElse($supplier);

        $this->assertSame(1, $a);
        $this->assertSame(99, $b);
        $this->assertSame(1, $calls, 'supplier must be lazy and called exactly once');

        $calls = 0;
        $s2 = function () use (&$calls) { $calls++; return new Right(5); };

        $c = new Right(2)->orElse($s2);
        $d = new Left('e')->orElse($s2);

        $this->assertSame(2, $c->getOrElse(-1));
        $this->assertSame(5, $d->getOrElse(-1));
        $this->assertSame(1, $calls, 'orElse supplier called only for Left');
    }
}
