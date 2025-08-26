<?php declare(strict_types=1);

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
