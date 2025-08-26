<?php declare(strict_types=1);

    /**
     * @codeCoverageIgnore
     */
    /**
     * Until I determine what is different from local env and CI env
     */


    namespace PenguinPark\Monad\Validation;

use PenguinPark\Monad\Typeclass\Apply;

/**
 * @template E of array<int, mixed>
 * @template A
 * @extends Validation<E, A>
 */
final class Invalid extends Validation
{
    /** @var E */
    private array $errors;

    /** @param E $errors */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /** @inheritDoc */
    public function ap(self|Apply $fa): Validation
    {
        // Accumulate errors from both sides (Semigroup = array_merge)
        return $fa->fold(
            fn (array $errsB) => new self(array_merge($this->errors, $errsB)),
            fn (mixed $_) => $this // other side valid; keep our errors
        );
    }

    /** @return E */
    public function errors(): array
    {
        return $this->errors;
    }

    /** @inheritDoc */
    public function fold(callable $onInvalid, callable $onValid): mixed
    {
        return $onInvalid($this->errors);
    }

    public function isValid(): bool
    {
        return false;
    }

    /** @inheritDoc */
    public function map(callable $f): Validation
    {
        // ignore f when invalid
        return $this;
    }

    public static function pure(mixed $a): Invalid
    {
        return self::invalid($a);
    }
}
