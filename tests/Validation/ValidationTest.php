<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Validation;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Validation\Validation;
use PenguinPark\Monad\Validation\Invalid;

final class ValidationTest extends TestCase
{
    public function testValidMap(): void
    {
        $v = Validation::of(2)->map(fn (int $x) => $x + 1);
        $out = $v->fold(fn ($e) => null, fn ($a) => $a);
        $this->assertSame(3, $out);
    }

    public function testInvalidMapIsNoOp(): void
    {
        $inv = Validation::invalid('error-1')->map(fn ($x) => $x);
        $this->assertTrue($inv->isInvalid());
        $errs = $inv->fold(fn (array $e) => $e, fn ($_) => []);
        $this->assertSame(['error-1'], $errs);
    }

    public function testApValidFunctionAndValidArgs(): void
    {
        $sum2 = Validation::of(fn (int $a) => fn (int $b) => $a + $b);

        $result = $sum2
            ->ap(Validation::of(2))
            ->ap(Validation::of(3));

        $val = $result->fold(fn ($e) => null, fn ($a) => $a);
        $this->assertSame(5, $val);
        $this->assertTrue($result->isValid());
    }

    public function testApAccumulatesErrorsOnBothSides(): void
    {
        $pair = Validation::of(fn ($a, $b) => [$a, $b]);

        $left  = Validation::invalid('E-left');
        $right = Validation::invalid('E-right');

        $res = $pair->ap($left)->ap($right);

        $this->assertTrue($res->isInvalid());
        $errs = $res->fold(fn (array $e) => $e, fn ($_) => []);
        $this->assertSame(['E-left', 'E-right'], $errs);
    }

    public function testApWithOneInvalidAndOneValid(): void
    {
        $pair = Validation::of(fn ($a, $b) => [$a, $b]);

        $left  = Validation::invalid('E-left');
        $right = Validation::of('ok-right');

        $res = $pair->ap($left)->ap($right);

        $this->assertTrue($res->isInvalid());
        $errs = $res->fold(fn (array $e) => $e, fn ($_) => []);
        $this->assertSame(['E-left'], $errs);
    }

    public function testFold(): void
    {
        $ok = Validation::of('A')->fold(fn ($e) => null, fn ($a) => $a);
        $this->assertSame('A', $ok);

        $bad = Validation::invalid(['x','y'])->fold(fn ($e) => $e, fn ($a) => null);
        $this->assertSame(['x','y'], $bad);
    }

    public function testGetOrElse(): void
    {
        $v = Validation::of(42)->getOrElse(0);
        $this->assertSame(42, $v);

        $i = Validation::invalid(['e1','e2'])->getOrElse(fn (array $errs) => count($errs));
        $this->assertSame(2, $i);
    }

    public function testIsValidFlags(): void
    {
        $this->assertTrue(Validation::of('x')->isValid());
        $this->assertFalse(Validation::invalid('err')->isValid());
        $this->assertTrue(Validation::invalid('err')->isInvalid());
    }

    public function testInvalidHelperWrapsScalar(): void
    {
        $inv = Validation::invalid('one');
        $errs = $inv->fold(fn (array $e) => $e, fn ($_) => []);
        $this->assertSame(['one'], $errs);
    }

    public function testErrorsAccessor(): void
    {
        $inv = new Invalid(['e1','e2']);
        $this->assertSame(['e1','e2'], $inv->errors());
    }
}
