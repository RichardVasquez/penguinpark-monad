<?php declare(strict_types=1);

namespace PenguinPark\Monad\Exception;

use RuntimeException;
// Unsafe unwraps (trying to extract from Nothing/Left/etc.)
class UnwrapException extends RuntimeException implements MonadException {}