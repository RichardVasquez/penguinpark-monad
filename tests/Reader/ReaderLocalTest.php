<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Reader;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Reader\Reader;

final class ReaderLocalTest extends TestCase
{
    public function testLocalOverridesEnvForSubComputationOnly(): void
    {
        $timeoutR = Reader::ask()->map(fn (array $env) => $env['timeout'] ?? 30);

        $faster = $timeoutR->local(fn (array $env) => [...$env, 'timeout' => 1]);

        $env = ['timeout' => 10];

        // local affects only the 'faster' reader
        $this->assertSame(1,  $faster->run($env));
        $this->assertSame(10, $timeoutR->run($env));
    }
}
