<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\State;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\State\State;

final class StateChildBindCountTest extends TestCase
{
    public function testAllChildAndTailBindsExecute(): void
    {
        $count = 0;

        $inc = function ($v) use (&$count) {
            $count++;
            return $v;
        };

        $child = State::of('x')->map($inc)->map($inc);          // 2
        $prog  = State::of(null)
            ->flatMap(fn ($_) => $child)
            ->map($inc)                                         // +1
            ->map($inc);                                        // +1

        $prog->run('state');

        $this->assertSame(4, $count, 'two child binds + two tail binds must all run');
    }
}
