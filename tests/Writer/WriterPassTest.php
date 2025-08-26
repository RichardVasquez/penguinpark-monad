<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Writer;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Writer\Writer;
use PenguinPark\Monad\Exception\ContractViolation;

final class WriterPassTest extends TestCase
{
    public function testPassAppliesLogTransformerAndKeepsValue(): void
    {
        $w = Writer::tell('a')
            ->map(fn () => ['val', fn (array $log): array => array_merge($log, ['b'])]);

        $out = $w->pass();
        [$log, $val] = $out->run(); // run() returns [log, value]

        $this->assertSame('val', $val);
        $this->assertSame(['a','b'], $log);
    }

    public function testPassThrowsWhenValueIsNotArrayPair(): void
    {
        $w1 = Writer::of('x'); // not a pair
        $this->expectException(ContractViolation::class);
        $w1->pass();
    }

    public function testPassThrowsWhenPairCountIsOne(): void
    {
        $w = Writer::of(['only']);
        $this->expectException(ContractViolation::class);
        $w->pass();
    }

    public function testPassThrowsWhenPairCountIsThree(): void
    {
        $w = Writer::of(['a','b','c']);
        $this->expectException(ContractViolation::class);
        $w->pass();
    }

    public function testPassThrowsWhenSecondElementNotCallable(): void
    {
        $w = Writer::of(['a', 'not-callable']);
        $this->expectException(ContractViolation::class);
        $w->pass();
    }
}
