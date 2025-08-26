<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Left;
use RuntimeException;
use Throwable;
final class EitherGetOrThrowStringMapperTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testLeftMapperReturningStringBecomesMessage(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('mapped');

        new Left('L')->getOrThrow(fn ($l) => 'mapped');
    }
}
