<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\State;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\State\State;

final class StateChildBindOrderTest extends TestCase
{
    public function testChildBindsRunInFullThenTailBinds(): void
    {
        $log = [];

        // Child contributes TWO binds via map
        $child =
            State::of('seed')
                ->map(function ($v) use (&$log) {
                    $log[] = 'child1';
                    return $v; // keep value
                })
                ->map(function ($v) use (&$log) {
                    $log[] = 'child2';
                    return 'child-done'; // final child value
                });

        // Program: bring in child's binds, then two tail maps
        $prog = State::of(null)
            ->flatMap(fn ($_) => $child)
            ->map(function ($v) use (&$log) {
                $log[] = 'tail1';
                return $v;
            })
            ->map(function ($v) use (&$log) {
                $log[] = 'tail2';
                return $v;
            });

        [$s, $v] = $prog->run(99);

        $this->assertSame(['child1','child2','tail1','tail2'], $log, 'must not skip the first child bind; tail must follow');
        $this->assertSame(99, $s, 'map-only chain must not change state');
        $this->assertSame('child-done', $v);
    }

    public function testSingleChildBindThenTail(): void
    {
        $log = [];

        $child = State::of('seed')->map(function ($v) use (&$log) {
            $log[] = 'child1';
            return 'child-done';
        });

        $prog = State::of(null)
            ->flatMap(fn($_) => $child)
            ->map(function ($v) use (&$log) {
                $log[] = 'tail1';
                return $v;
            })
            ->map(function ($v) use (&$log) {
                $log[] = 'tail2';
                return $v;
            });

        [$s, $v] = $prog->run(0);

        $this->assertSame(['child1', 'tail1', 'tail2'], $log);
        $this->assertSame(0, $s);
        $this->assertSame('child-done', $v);
    }

    public function testTwoChildBindsThenTail(): void
    {
        $log = [];

        $child = State::of('x')
            ->map(function ($v) use (&$log) {
                $log[] = 'child1';
                return $v;
            })
            ->map(function ($v) use (&$log) {
                $log[] = 'child2';
                return 'done';
            });

        $prog = State::of(null)
            ->flatMap(fn($_) => $child)
            ->map(function ($v) use (&$log) {
                $log[] = 'tail1';
                return $v;
            })
            ->map(function ($v) use (&$log) {
                $log[] = 'tail2';
                return $v;
            });

        [$s, $v] = $prog->run('S');

        $this->assertSame(['child1', 'child2', 'tail1', 'tail2'], $log);
        $this->assertSame('S', $s);
        $this->assertSame('done', $v);
    }
}
