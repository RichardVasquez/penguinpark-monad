<?php /** @noinspection PhpUnused */

declare(strict_types=1);

namespace PenguinPark\Monad\Either;
use PenguinPark\Monad\Exception\ContractViolation;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Exception\UnwrapLeft;
use PenguinPark\Monad\Maybe\Maybe;
use PenguinPark\Monad\Typeclass\Apply;
use PenguinPark\Monad\Util\Eq;
use RuntimeException;
use Throwable;

/**
 * @template L
 * @template R
 * @extends Either<L,R>
 */
final class Left extends Either
{
    /** @var L */
    private mixed $value;

    /**
     * @param L $value
     */
    public function __construct(mixed $value) { $this->value = $value; }

    /**
     * String representation for debugging/logging.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'Left(' . (is_object($this->value)
                ? get_class($this->value)
                : var_export($this->value, true)) . ')';
    }

    /** @inheritDoc */
    public function ap(Apply|self $fa): Either
    {
        // Function-in-context is Left => stay Left regardless of the argument
        return $this;
    }

    /**
     * No-op side-effect compatibility method; returns self unchanged.
     *
     * @param callable(L):void $function Ignored for Left
     * @return Left
     */
    public function apply(callable $function): Left
    {
        // no-op
        return $this;
    }

    /**
     * Structural equality: same variant (Left) and valueEq on payload.
     *
     * @param mixed $other
     * @return bool
     */
    public function equals(mixed $other): bool
    {
        return $other instanceof Left && Eq::valueEq($this->value, $other->value);
    }

    /** @inheritDoc */
    public function flatMap(callable $function): Left { return $this; }

    /** @inheritDoc */
    public function fold(callable $onLeft, callable $onRight): mixed
    {
        return $onLeft($this->value);
    }

    /**
     * Unsafe unwrap; always throws for Left values.
     *
     * @throws RuntimeException
     */
    public function get(): mixed
    {
        throw new UnwrapLeft('Cannot get value from Left');
    }

    public function getLeft(): Maybe
    {
        // Return the underlying Left payload as Maybe
        return Maybe::just($this->value);
    }

    /** @inheritDoc */
    public function getOrElse(mixed $default): mixed
    {
        return is_callable($default) ? $default() : $default;
    }

    /**
     * @throws Throwable
     */
    public function getOrThrow(Throwable|string|callable $throwable = 'Left value'): mixed
    {
        // Mapper branch
        if (is_callable($throwable)) {
            $mapped = $throwable($this->value);

            if ($mapped instanceof Throwable) {
                throw $mapped;
            }
            if (is_string($mapped)) {
                throw new UnwrapLeft($mapped);
            }
            elseif (is_scalar($mapped)) {
                throw new UnwrapLeft((string) $mapped);
            }
            throw new UnwrapLeft('Left value');
        }

        // Direct Throwable
        if ($throwable instanceof Throwable) {
            throw $throwable;
        }

        // By signature, anything else here is a string
        throw new UnwrapLeft($throwable);
    }

    public function getRight(): Maybe
    {
        // No Right payload on Left
        return Maybe::nothing();
    }

    /**
     * @return bool True; this is the Left branch
     */
    public function isLeft(): bool { return true; }

    /**
     * @return bool False; this is not the Right branch
     */
    public function isRight(): bool { return false; }

    /** @inheritDoc */
    public function map(callable $f): self { return $this; }

    /** @inheritDoc */
    public function mapLeft(callable $f): Either
    {
        return new Left($f($this->value));
    }

    /**
     * Construct a Left from a raw value (alias of constructor).
     *
     * @param mixed $value
     * @return static
     */
    public static function of(mixed $value): self
    {
        return new self($value);
    }

    /** @inheritDoc */
    public function orElse(callable $supplier): Either
    {
        $res = $supplier();
        if (!$res instanceof Either) {
            throw new InvalidBindReturnType('Left::orElse supplier must return an Either');
        }
        return $res;
    }

    /**
     * @throws Throwable
     */
    public function orThrow(callable $mapLeftToException): mixed
    {
        $ex = $mapLeftToException($this->value);
        if (!$ex instanceof Throwable) {
            throw new ContractViolation('Left::orThrow mapper must return a Throwable');
        }
        throw $ex;
    }

    /** @inheritDoc */
    public function swap(): Either { return new Right($this->value); }

    public function tap(callable $effect): self
    {
        // no-op on Left
        return $this;
    }

    public function tapLeft(callable $effect): self
    {
        $effect($this->value);   // run only on Left
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
