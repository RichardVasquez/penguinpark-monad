<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Exceptions;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Reader\Reader;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Exception\ApCallableExpected;

final class ReaderExceptionsTest extends TestCase
{
    public function testFlatMapMustReturnReader(): void
    {
        $this->expectException(InvalidBindReturnType::class);
        Reader::of(1)->flatMap(fn ($x) => 'nope')->run([]);
    }

    public function testApRequiresCallableHeldByReceiver(): void
    {
        $this->expectException(ApCallableExpected::class);
        Reader::of(123)->ap(Reader::of('x'))->run([]);
    }
}
