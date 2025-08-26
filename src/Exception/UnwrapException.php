<?php declare(strict_types=1);

    /**
     * @codeCoverageIgnore
     */
    /**
     * Until I determine what is different from local env and CI env
     */


    namespace PenguinPark\Monad\Exception;

use RuntimeException;
// Unsafe unwraps (trying to extract from Nothing/Left/etc.)
class UnwrapException extends RuntimeException implements MonadException {}
