<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Reader;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Reader\Reader;

final class ReaderBasicsTest extends TestCase
{
    public function testAskAndMapAndRun(): void
    {
        $getTimeout = Reader::ask()->map(fn (array $env) => $env['timeout'] ?? 30);

        $this->assertSame(30, $getTimeout->run(['timeout' => 30]));
        $this->assertSame(7,  $getTimeout->run(['timeout' => 7]));
        $this->assertSame(30, $getTimeout->run([])); // default behavior in lambda
    }

    public function testOfIgnoresEnvironment(): void
    {
        $r = Reader::of('static');
        $this->assertSame('static', $r->run(['anything' => 'goes']));
    }
}
