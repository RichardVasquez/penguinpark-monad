<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Exceptions;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\ContractViolation;

final class ListMExceptionsTest extends TestCase
{
    public function testFlatMapMustReturnListM(): void
    {
        $this->expectException(InvalidBindReturnType::class);
        ListM::of(1)->flatMap(fn($x) => 123);
    }

    public function testApRequiresReceiverToHoldCallables(): void
    {
        $this->expectException(ApCallableExpected::class);
        ListM::of(123)->ap(ListM::of(1));
    }

    public function testReduce1OnEmptyThrowsContractViolation(): void
    {
        $this->expectException(ContractViolation::class);
        ListM::fromArray([])->reduce1(fn($a, $x) => $a);
    }
}
