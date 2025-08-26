<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\IO;

use PenguinPark\Monad\IO\IO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class IOBracketTest extends TestCase
{
    public function testBracketReleasesOnSuccess(): void
    {
        $log = [];

        $acquire = IO::delay(function () use (&$log) { $log[] = 'acquire'; return 'R'; });

        $use = function (string $r) use (&$log): IO {
            return IO::delay(function () use (&$log, $r) {
                $log[] = "use:$r";
                return 123;
            });
        };

        $release = function (string $r) use (&$log): IO {
            return IO::delay(function () use (&$log, $r) {
                $log[] = "release:$r";
            });
        };

        $out = IO::bracket($acquire, $use, $release)->unsafeRun();

        $this->assertSame(123, $out);
        $this->assertSame(['acquire','use:R','release:R'], $log);
    }

    public function testBracketReleasesOnFailureAndRethrows(): void
    {
        $log = [];

        $acquire = IO::delay(function () use (&$log) { $log[] = 'acquire'; return 'R'; });

        $use = function (string $r) use (&$log): IO {
            return IO::delay(function () use (&$log, $r) {
                $log[] = "use:$r";
                throw new RuntimeException('fail');
            });
        };

        $release = function (string $r) use (&$log): IO {
            return IO::delay(function () use (&$log, $r) {
                $log[] = "release:$r";
            });
        };

        try {
            IO::bracket($acquire, $use, $release)->unsafeRun();
            $this->fail('Expected exception');
        } catch (RuntimeException $e) {
            $this->assertSame('fail', $e->getMessage());
        }

        $this->assertSame(['acquire','use:R','release:R'], $log);
    }
}
