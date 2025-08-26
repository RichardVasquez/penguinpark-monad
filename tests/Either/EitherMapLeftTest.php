<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Either\Left;

final class EitherMapLeftTest extends TestCase
{
    public function testMapLeftTransformsLeftOnly(): void
    {
        $a = new Left('e')->mapLeft(fn (string $s) => strtoupper($s));
        $b = new Right(1)->mapLeft(fn ($s) => 'nope');

        $this->assertTrue($a->isLeft());
        $this->assertSame('E', $a->fold(fn ($l) => $l, fn ($r) => $r));
        $this->assertTrue($b->isRight());
        $this->assertSame(1, $b->getOrElse(-1));
    }

    public function testBimap(): void
    {
        $l = new Left('e')->bimap(fn ($l) => "L:$l", fn ($r) => "R:$r");
        $r = new Right(2)->bimap(fn ($l) => "L:$l", fn ($r) => "R:$r");

        $this->assertSame('L:e', $l->fold(fn ($l) => $l, fn ($r) => $r));
        $this->assertSame('R:2', $r->fold(fn ($l) => $l, fn ($r) => $r));
    }
}
