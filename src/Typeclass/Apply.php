<?php declare(strict_types=1);

namespace PenguinPark\Monad\Typeclass;

/**
 * @template T
 * @extends Functor<T>
 */
interface Apply extends Functor
{
    /**
     * Apply a wrapped function to a wrapped value.
     *
     * @template U
     * @param Apply $fa Receiver holds callable(T):U
     * @return Apply Result holds U
     */
    public function ap(self $fa): self|static;
}
