<?php declare(strict_types=1);

namespace PenguinPark\Tests\Monad\Writer;

use PHPUnit\Framework\TestCase;
use PenguinPark\Monad\Writer\Writer;

final class WriterNormalizationTest extends TestCase
{
    public function testCensorNormalizesNumericKeysAndKeepsOrder(): void
    {
        // start with a known log
        $w = Writer::tell('a')->flatMap(fn () => Writer::tell('b'));

        // censor returns sparse numeric keys; Writer must reindex to 0..n-1
        $w2 = $w->censor(fn (array $log) => [2 => strtoupper($log[0]), 5 => strtoupper($log[1])]);

        [$log, $val] = $w2->run();

        $this->assertSame(['A','B'], $log);
        $this->assertNull($val); // last tell yields null value
    }
}