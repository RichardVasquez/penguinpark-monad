<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Util;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\ListM\ListM;
use stdClass;

final class ListEqualsTypeGuardTest extends TestCase
{
    public function testEqualsWithNonListMIsFalse(): void
    {
        $list = ListM::fromArray([1]);
        $this->assertFalse($list->equals(new stdClass()));
        $this->assertFalse($list->equals('not-a-list'));
    }
}
