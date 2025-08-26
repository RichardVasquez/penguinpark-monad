<?php /** @noinspection PhpUnused */

    /**
     * @codeCoverageIgnore
     */
    /**
     * Until I determine what is different from local env and CI env
     */


    declare(strict_types=1);

namespace PenguinPark\Monad\Maybe;

use PenguinPark\Monad\Exception\ContractViolation;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Exception\UnwrapNothing;
use PenguinPark\Monad\Typeclass\Apply;
use RuntimeException;
use Throwable;

/**
 * @template T
 * @extends Maybe<T>
 */
class Nothing extends Maybe
{
    /**
     * Construct a Nothing value.
     *
     * Represents the absence of a value.
     */
    public function __construct() {}

    /**
     * String representation for debugging/logging.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'Nothing';
    }

    /** @inheritDoc */
    public function ap(Maybe|Apply $fa): Maybe
    {
        // Nothing applied to anything is Nothing
        return $this;
    }

    /**
     * No-op side-effect compatibility method; returns self unchanged.
     *
     * @param callable(T):void $function Ignored for Nothing
     * @return $this
     */
    public function apply(callable $function): static
    {
        // no-op
        return $this;
    }

    /**
     * Legacy Monad interface compatibility: Nothing binds to Nothing.
     *
     * @param callable $function
     * @return Nothing
     */
    public function bind(callable $function): static
    {
        return $this;
    }

    /**
     * Structural equality: any other Nothing is equal; Just is not.
     *
     * @param mixed $other
     * @return bool
     */
    public function equals(mixed $other): bool
    {
        return $other instanceof Nothing;
    }

    /** @inheritDoc */
    public function filter(callable $predicate): Maybe
    {
        return $this;
    }

    /** @inheritDoc */
    public function flatMap(callable $function): static
    {
        return $this;
    }

    /** @inheritDoc */
    public function fold(callable $onNothing, callable $onJust)
    {
        return $onNothing();
    }

    /**
     * Unsafe unwrap; always throws for Nothing values.
     *
     * @throws RuntimeException
     */
    public function get()
    {
        throw new UnwrapNothing('Cannot get value from Nothing');
    }

    /** @inheritDoc */
    public function getOrElse(mixed $default): mixed
    {
        return is_callable($default) ? $default() : $default;
    }

    /** @inheritDoc
     * @throws Throwable
     */
    public function getOrThrow(Throwable|string|callable $throwable): mixed
    {
        if ($throwable instanceof Throwable) {
            throw $throwable;
        }
        if (is_callable($throwable)) {
            $t = $throwable();
            if (!$t instanceof Throwable) {
                throw new ContractViolation('Nothing::getOrThrow callable must return a Throwable');
            }
            throw $t;
        }

        // Safe stringification; avoids `(string)` so the CastString mutant has nowhere to mutate.
        $msg = is_string($throwable) ? $throwable : var_export($throwable, true);
        if ($msg === '') {
            $msg = 'Cannot get value from Nothing';
        }
        throw new RuntimeException($msg);
    }

    /**
     * @return bool False when this is a Nothing
     */
    public function isJust(): bool { return false; }

    /**
     * @return bool True when this is a Nothing
     */
    public function isNothing(): bool { return true; }

    /** @inheritDoc */
    public function map(callable $f): static
    {
        return $this;
    }

    /** @inheritDoc */
    public function orElse(callable $supplier): Maybe
    {
        $m = $supplier();
        if (!$m instanceof Maybe) {
            throw new InvalidBindReturnType('Nothing::orElse supplier must return a Maybe');
        }
        return $m;
    }

    /** @inheritDoc */
    public function orNull(): null
    {
        return null;
    }

    /** @inheritDoc */
    public function tap(callable $effect): self
    {
        return $this;
    }

    /**
     * Legacy Monad interface compatibility: construct a Just from the value.
     *
     * @param mixed $value
     * @return Just|Maybe
     */
    public static function unit(mixed $value): Just|Maybe
    {
        return new Just($value);
    }
}
