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
 * @extends Applicative<T>
 * @extends Bind<T>
 */
interface Monad extends Applicative, Bind {}
