<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Maybe;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Maybe\Just;
use ReflectionMethod;

final class JustGetVisibilityTest extends TestCase
{
    public function testJustGetIsPublicAndReturnsValue(): void
    {
        $rm = new ReflectionMethod(Just::class, 'get');
        $this->assertTrue($rm->isPublic(), 'Just::get must stay public');

        $j = new Just('x');
        $this->assertSame('x', $j->get());
    }
}
