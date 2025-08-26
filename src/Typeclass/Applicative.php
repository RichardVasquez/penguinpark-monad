<?php declare(strict_types=1);

    /**
     * @codeCoverageIgnore
     */
    /**
     * Until I determine what is different from local env and CI env
     */


    namespace PenguinPark\Monad\Typeclass;

/**
 * @template T
 * @extends Apply<T>
 */
interface Applicative extends Apply
{
    /**
     * Alias of pure().
     * @template A
     * @param A $value
     * @return Applicative
     */
    public static function of(mixed $value): static|self;

    /**
     * Lift a pure value.
     * @template A
     * @param A $a
     * @return Applicative
     */
    public static function pure(mixed $a): static|self;
}
