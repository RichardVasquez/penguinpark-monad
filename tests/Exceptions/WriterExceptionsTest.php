<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Exceptions;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Writer\Writer;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\ContractViolation;
final class WriterExceptionsTest extends TestCase
{
    public function testFlatMapMustReturnWriter(): void
    {
        $this->expectException(InvalidBindReturnType::class);
        Writer::of(1)->flatMap(fn ($x) => 'nope');
    }

    public function testApRequiresCallableHeldByReceiver(): void
    {
        $this->expectException(ApCallableExpected::class);
        Writer::of(123)->ap(Writer::of(1));
    }

    public function testPassRequiresPairWithCallable(): void
    {
        $this->expectException(ContractViolation::class);
        Writer::of([1, 'not callable'])->pass();
    }

    public function testCensorMustReturnArray(): void
    {
        $this->expectException(ContractViolation::class);
        Writer::of(1)->censor(fn (array $log) => 'not array');
    }
}
