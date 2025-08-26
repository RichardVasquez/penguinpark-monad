<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use InvalidArgumentException;
use PenguinPark\Monad\Either\Left;
use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Right;
use RuntimeException;
use Throwable;

final class EitherGetOrThrowTest extends TestCase
{
    public function testRightIgnoresParamAndReturns(): void
    {
        $this->assertSame(42, new Right(42)->getOrThrow('nope'));
    }

    /**
     * @throws Throwable
     */
    public function testLeftThrowsRuntimeWithMessage(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('boom');

        new Left('L')->getOrThrow('boom');
    }

    /**
     * @throws Throwable
     */
    public function testLeftThrowsProvidedThrowable(): void
    {
        $ex = new InvalidArgumentException('bad');

        try {
            new Left('L')->getOrThrow($ex);
        } catch (InvalidArgumentException $e) {
            $this->assertSame($ex, $e); // same instance rethrown
        }
    }

    /**
     * @throws Throwable
     */
    public function testLeftMapperCalledOnceAndCanReturnThrowable(): void
    {
        $calls = 0;
        try {
            new Left('L')->getOrThrow(function ($l) use (&$calls) {
                $calls++;
                return new RuntimeException("L=$l");
            });
            //$this->fail('Expected RuntimeException');
        } catch (RuntimeException $e) {
            $this->assertSame('L=L', $e->getMessage());
            $this->assertSame(1, $calls);
        }
    }

    /**
     * @throws Throwable
     */
    public function testLeftMapperReturningStringBecomesRuntime(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('mapped');

        new Left('L')->getOrThrow(fn ($l) => 'mapped');
    }
}
