<?php /** @noinspection PhpUnused */

declare(strict_types=1);

namespace PenguinPark\Monad\Maybe;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Typeclass\Apply;
use PenguinPark\Monad\Util\Eq;
use Throwable;

/**
 * @template T
 * @extends Maybe<T>
 */
class Just extends Maybe
{
    /** @var T */
    private mixed $value;

    /**
     * @param T $value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * String representation for debugging/logging.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'Just(' . (is_object($this->value)
                ? get_class($this->value)
                : var_export($this->value, true)) . ')';
    }

    /** @inheritDoc */
    public function ap(Maybe|Apply $fa): Maybe
    {
        $f = $this->value;
        if (!is_callable($f)) {
            throw new ApCallableExpected('Maybe::ap expects the receiver to hold a callable');
        }
        if ($fa instanceof Nothing) {
            return $fa;
        }
        /** @var Just $fa */
        return new Just($f($fa->get()));
    }

    /**
     * Side-effect only; returns self for chaining.
     * Matches Monad::apply(callable): static
     *
     * @param callable(T):void $function
     * @return $this
     */
    public function apply(callable $function): static
    {
        $function($this->value);
        return $this;
    }

    /**
     * Legacy Monad interface compatibility: bind == flatMap.
     *
     * @param callable $function
     * @return Just
     */
    public function bind(callable $function): static
    {
        $m = $function($this->value);
        if (!$m instanceof Maybe) {
            throw new InvalidBindReturnType('Just::bind must return a Maybe');
        }
        return $m;
    }

    /**
     * Structural equality: same variant (Just) and valueEq on payload.
     *
     * @param mixed $other
     * @return bool
     */
    public function equals(mixed $other): bool
    {
        return $other instanceof Just && Eq::valueEq($this->value, $other->value);
    }

    /** @inheritDoc */
    public function filter(callable $predicate): Maybe
    {
        return $predicate($this->value) ? $this : new Nothing();
    }

    /** @inheritDoc */
    public function flatMap(callable $function): Maybe
    {
        $m = $function($this->value);
        if (!$m instanceof Maybe) {
            throw new InvalidBindReturnType('Just::flatMap must return a Maybe');
        }
        return $m;
    }

    /** @inheritDoc */
    public function fold(callable $onNothing, callable $onJust)
    {
        return $onJust($this->value);
    }

    /**
     * Unwrap. Safe for Just.
     *
     * @return T
     */
    public function get()
    {
        return $this->value;
    }

    /** @inheritDoc */
    public function getOrElse(mixed $default): mixed
    {
        return $this->value;
    }

    /** @inheritDoc */
    public function getOrThrow(Throwable|string|callable $throwable): mixed
    {
        return $this->value;
    }

    /**
     * @return bool True when this is a Just
     */
    public function isJust(): bool { return true; }

    /**
     * @return bool False when this is a Just
     */
    public function isNothing(): bool { return false; }

    /** @inheritDoc */
    public function map(callable $f): static
    {
        return new Just($f($this->value));
    }

    /** @inheritDoc */
    public function orElse(callable $supplier): Maybe
    {
        return $this;
    }

    /** @inheritDoc */
    public function orNull(): mixed
    {
        return $this->value;
    }

    /** @inheritDoc */
    public function tap(callable $effect): self
    {
        $effect($this->value);
        return $this;
    }

    /**
     *  Legacy Monad interface compatibility: alias for of/just.
     *
     * @param mixed $value
     * @return Just|Maybe
     */
    public static function unit(mixed $value): Just|Maybe
    {
        return new Just($value);
    }
}
