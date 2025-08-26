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
interface Bind extends Apply
{
    /**
     * Alias of flatMap().
     * @template U
     * @param callable(T):static $function
     * @return static
     */
    public function bind(callable $function): static|self;

    /**
     * @template U
     * @param callable(T):static $function
     * @return Bind
     */
    public function flatMap(callable $function): static|self;
}
