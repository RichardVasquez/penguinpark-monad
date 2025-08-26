<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Util;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Maybe;
use stdClass;

final class MaybeEqualsExtraTest extends TestCase
{
    public function testFloatInequalityAndNanVsNumber(): void
    {
        $this->assertFalse(Maybe::just(NAN)->equals(Maybe::just(1.23)));
        $this->assertFalse(Maybe::just(1.23)->equals(Maybe::just(NAN)));
        $this->assertFalse(Maybe::just(1.0)->equals(Maybe::just(1.0000001)));
    }

    public function testMixedTypesInequalityAndObjects(): void
    {
        $this->assertFalse(Maybe::just(1)->equals(Maybe::just('1')));

        $o1 = new stdClass(); $o1->x = 1;
        $o2 = new stdClass(); $o2->x = 1;

        $this->assertTrue(Maybe::just($o1)->equals(Maybe::just($o1)));
        $this->assertFalse(Maybe::just($o1)->equals(Maybe::just($o2)));
    }

    public function testArrayLengthMismatchFalse(): void
    {
        $this->assertFalse(Maybe::just(['a' => 1])->equals(Maybe::just(['a' => 1, 'b' => 2])));
    }
}
