<?php declare(strict_types=1);

namespace PenguinPark\Monad\Typeclass;

/**
 * @template T
 * @extends Applicative<T>
 * @extends Bind<T>
 */
interface Monad extends Applicative, Bind {}
