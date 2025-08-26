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
 */
interface Functor
{
    /**
     * @template U
     * @param callable(T):U $f
     * @return Functor
     */
    public function map(callable $f): static|self;
}
