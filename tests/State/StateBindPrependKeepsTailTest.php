<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\State;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\State\State;

final class StateBindPrependKeepsTailTest extends TestCase
{
    public function testChildBindsArePrependedButTailIsPreserved(): void
    {
        $log = [];

        // Child contributes ONE internal bind via map
        $child = State::of('x')
            ->map(function ($v) use (&$log) {
                $log[] = 'childMap';
                return 'child-done';
            });

        // Program: child first (so its bind gets PREPENDED), then two tail maps
        $prog = State::of(null)
            ->flatMap(fn ($_) => $child)
            ->map(function ($v) use (&$log) {
                $log[] = 'tail1';
                return $v; // keep value
            })
            ->map(function ($v) use (&$log) {
                $log[] = 'tail2';
                return $v; // keep value
            });

        [$s, $v] = $prog->run(0);

        // With correct prepend: we see childMap, then both tails, in order
        $this->assertSame(['childMap','tail1','tail2'], $log, 'child binds must be prepended; tail must be preserved');
        $this->assertSame(0, $s, 'state unchanged by map-only chain');
        $this->assertSame('child-done', $v, 'value from child map must flow through tail maps unchanged');
    }
}
