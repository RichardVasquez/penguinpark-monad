<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Either;
use RuntimeException;
use Throwable;

final class EitherTryCatchTest extends TestCase
{
    public function testReturnsRightOnSuccess(): void
    {
        $e = Either::tryCatch(fn () => 21 * 2);
        $this->assertTrue($e->isRight());
        $this->assertSame(42, $e->getOrElse(null));
    }

    public function testReturnsLeftOnThrowable(): void
    {
        $e = Either::tryCatch(function () { throw new RuntimeException('boom'); });
        $this->assertTrue($e->isLeft());
        $this->assertInstanceOf(Throwable::class, $e->fold(fn($l) => $l, fn($r)=>$r));
    }

    public function testMapsThrowableToDomainLeft(): void
    {
        $e = Either::tryCatch(
            fn () => throw new InvalidArgumentException('bad'),
            fn (Throwable $t) => 'ERR:'.$t->getMessage()
        );
        $this->assertTrue($e->isLeft());
        $this->assertSame('ERR:bad', $e->fold(fn($l)=>$l, fn($r)=>$r));
    }

    public function testMapperIsLazyAndCalledOnce(): void
    {
        $calls = 0;
        $e = Either::tryCatch(
            fn () => throw new RuntimeException('x'),
            function (Throwable $t) use (&$calls) { $calls++; return 'L'; }
        );
        $this->assertSame(1, $calls);
        $this->assertSame('L', $e->fold(fn($l)=>$l, fn($r)=>$r));
    }
}
