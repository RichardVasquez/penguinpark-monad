<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use PenguinPark\Monad\Either\Left;
use PenguinPark\Monad\Either\Right;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

final class EitherGetOrThrowEdgeCasesTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testLeftMapperReturningNonScalarArrayFallsBackToDefaultMessage(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Left value');

        new Left('L')->getOrThrow(fn ($l) => ['not' => 'scalar']);
    }

    /**
     * @throws Throwable
     */
    public function testLeftMapperReturningNonScalarObjectFallsBackToDefaultMessage(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Left value');

        new Left('L')->getOrThrow(fn ($l) => new \stdClass());
    }

    /**
     * @throws Throwable
     */
    public function testLeftMapperReturningScalarIsStringified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('123');

        new Left('L')->getOrThrow(fn ($l) => 123);
    }

    public function testRightStillReturnsValue(): void
    {
        $this->assertSame(42, new Right(42)->getOrThrow('ignored'));
    }
}