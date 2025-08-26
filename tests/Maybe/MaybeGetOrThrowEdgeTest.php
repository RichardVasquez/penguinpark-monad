<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Maybe;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Maybe;
use RuntimeException;

final class MaybeGetOrThrowEdgeTest extends TestCase
{
    public function testNonStringMessageIsStringCast(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('123'); // must string-cast, not TypeError
        Maybe::nothing()->getOrThrow('123');
    }

    public function testEmptyStringFallsBackToDefaultMessage(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot get value from Nothing');
        Maybe::nothing()->getOrThrow('');
    }

    public function testNonEmptyStringIsUsedVerbatim(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('boom');
        Maybe::nothing()->getOrThrow('boom');
    }

    public function testCallableMustReturnThrowable(): void
    {
        $this->expectException(LogicException::class);
        Maybe::nothing()->getOrThrow(fn () => 'not-a-throwable');
    }

    public function testCallableThatReturnsThrowableIsThrown(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('kapow');
        Maybe::nothing()->getOrThrow(fn () => new InvalidArgumentException('kapow'));
    }
}
