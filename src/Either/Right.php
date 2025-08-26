<?php declare(strict_types=1);

namespace PenguinPark\Monad\Either;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Maybe\Maybe;
use PenguinPark\Monad\Typeclass\Apply;
use PenguinPark\Monad\Util\Eq;
use Throwable;

/**
 * @template L
 * @template R
 * @extends Either<L,R>
 */
final class Right extends Either
{
    /** @var R */
    private mixed $value;

    /**
     * @param R $value
     */
    public function __construct(mixed $value) { $this->value = $value; }

    /**
     * String representation for debugging/logging.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'Right(' . (is_object($this->value) ? get_class($this->value) : var_export($this->value, true)) . ')';
    }

    /** @inheritDoc */
    public function ap(self|Apply $fa): Either
    {
        $f = $this->value;
        if (!is_callable($f)) {
            throw new ApCallableExpected('Right::ap expects the receiver to hold a callable');
        }
        if ($fa instanceof Left) {
            return $fa; // propagate first Left
        }
        /** @var Right $fa */
        return new Right($f($fa->get()));
    }

    /**
     * Side-effect compatibility method; invokes the function with the Right value and returns self.
     *
     * @param callable(R):void $function
     * @return $this
     */
    public function apply(callable $function): Right
    {
        $function($this->value);
        return $this;
    }

    /**
     * Structural equality: same variant (Right) and valueEq on payload.
     *
     * @param mixed $other
     * @return bool
     */
    public function equals(mixed $other): bool
    {
        return $other instanceof Right && Eq::valueEq($this->value, $other->value);
    }

    /** @inheritDoc */
    public function flatMap(callable $function): Either
    {
        $res = $function($this->value);
        if (!$res instanceof Either) {
            throw new InvalidBindReturnType('Right::flatMap must return an Either');
        }
        return $res;
    }

    /** @inheritDoc */
    public function fold(callable $onLeft, callable $onRight): mixed
    {
        return $onRight($this->value);
    }

    /**
     * Unsafe unwrap; returns the Right value.
     *
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->value;
    }

    public function getLeft(): Maybe
    {
        // No Left payload on Right
        return Maybe::nothing();
    }

    /** @inheritDoc */
    public function getOrElse(mixed $default): mixed { return $this->value; }

    /**
     * @param Throwable|string|callable $throwable
     * @return mixed
     */
    public function getOrThrow(Throwable|string|callable $throwable = 'Right value'): mixed
    {
        return $this->value;
    }

    public function getRight(): Maybe
    {
        // Return the underlying Right payload as Maybe
        return Maybe::just($this->value);
    }

    /**
     * @return bool False; this is not the Left branch
     */
    public function isLeft(): bool { return false; }

    /**
     * @return bool True; this is the Right branch
     */
    public function isRight(): bool { return true; }

    /** @inheritDoc */
    public function map(callable $f): self
    {
        return new Right($f($this->value));
    }

    /** @inheritDoc */
    public function mapLeft(callable $f): Either { return $this; }

    /**
     * Construct a Right from a raw value (alias of constructor).
     *
     * @param mixed $value
     * @return self
     */
    public static function of(mixed $value): self { return new self($value); }

    /** @inheritDoc */
    public function orElse(callable $supplier): Either { return $this; }

    public function orThrow(callable $mapLeftToException): mixed
    {
        // Right path: just unwrap
        return $this->value;
    }

    /** @inheritDoc */
    public function swap(): Either { return new Left($this->value); }

    public function tap(callable $effect): self
    {
        $effect($this->value);   // run only on Right
        return $this;
    }

    public function tapLeft(callable $effect): self
    {
        // no-op on Right
        return $this;
    }

    /**
     * Legacy alias for of().
     *
     * @param mixed $value
     * @return self
     */
    public static function unit(mixed $value): self { return new self($value); }

}
