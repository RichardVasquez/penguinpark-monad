<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Either;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Either\Left;

final class EitherIntrospectionTest extends TestCase
{
    public function testIsLeftIsRightAndSwap(): void
    {
        $r = new Right('ok');
        $l = new Left('err');

        $this->assertTrue($r->isRight());
        $this->assertFalse($r->isLeft());

        $this->assertTrue($l->isLeft());
        $this->assertFalse($l->isRight());

        $this->assertTrue($r->swap()->isLeft());
        $this->assertTrue($l->swap()->isRight());
    }
}
