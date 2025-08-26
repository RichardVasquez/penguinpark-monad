<?php declare(strict_types=1);

namespace PenguinPark\Monad\Validation;

use ArgumentCountError;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\IO\IO;
use PenguinPark\Monad\ListM\ListM;
use PenguinPark\Monad\Reader\Reader;
use PenguinPark\Monad\Typeclass\Apply;

/**
 * @template E of array<int, mixed>
 * @template A
 * @extends Validation<E, A>
 */
final class Valid extends Validation
{
    /** @var A */
    private mixed $value;

    /** @param A $value */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /** @inheritDoc */
    public function ap(self|Apply $fa): Validation
    {
        if (!is_callable($this->value)) {
            // Behavior choice: either throw or keep as-is. Prefer explicit failure.
            throw new ApCallableExpected('Validation::ap requires a callable in Valid');
        }

        /** @var callable $f */
        $f = $this->value;

        return $fa->fold(
            fn (array $errs) => new Invalid($errs),
            function (mixed $b) use ($f) {
                // Try direct application. If callable expects >1 args, create a partial.
                try {
                    $result = $f($b);
                    return new self($result);
                } catch (ArgumentCountError) {
                    $partial = static function (...$rest) use ($f, $b) {
                        return $f($b, ...$rest);
                    };
                    return new self($partial);
                }
            }
        );
    }

    /** @inheritDoc */
    public function fold(callable $onInvalid, callable $onValid): mixed
    {
        return $onValid($this->value);
    }

    public function isValid(): bool
    {
        return true;
    }

    /** @inheritDoc */
    public function map(callable $f): Validation
    {
        return new self($f($this->value));
    }

    public static function pure(mixed $a): Valid|IO|ListM|Reader|self
    {
        return Validation::of($a);
    }
}
