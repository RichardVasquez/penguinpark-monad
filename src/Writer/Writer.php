<?php declare(strict_types=1);

namespace PenguinPark\Monad\Writer;

use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\ContractViolation;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\IO\IO;
use PenguinPark\Monad\Maybe\Maybe;
use PenguinPark\Monad\Typeclass\Apply;
use PenguinPark\Monad\Typeclass\Monad;

/**
 * @template A
 * Simple Writer with an array log (list of entries). Monoid is ([], array_merge).
 */
final class Writer implements Monad
{
    /** @var array<int, mixed> */
    private array $log;
    /** @var mixed */
    private mixed $value;

    /**
     * @param array<int, mixed> $log
     * @param mixed $value
     */
    private function __construct(array $log, mixed $value)
    {
        // normalize indices for predictability
        $this->log = array_values($log);
        $this->value = $value;
    }

    public function __toString(): string
    {
        $preview = count($this->log)
            ? ('[' . count($this->log) . ' entries]')
            : '[]';
        $v = is_scalar($this->value) || $this->value === null
            ? var_export($this->value, true)
            : get_debug_type($this->value);
        return "Writer(log=$preview, value=$v)";
    }

    /**
     * Applicative apply:
     *   this must hold a callable f; apply it to $fa's value; logs append L->R.
     * Writer<callable(A):B>::ap(Writer<A>) -> Writer<B>
     */
    public function ap(self|Apply $fa): self
    {
        $f = $this->value;
        if (!is_callable($f)) {
            throw new ApCallableExpected('Writer::ap expects the receiver to hold a callable');
        }
        // logs: first this (function holder), then argument
        return new self(array_merge($this->log, $fa->log), $f($fa->value));
    }

    /**
     * Side-effect only (tap): run effect on current value, keep value & log.
     * @param callable(mixed):void $effect
     * @return self
     */
    public function apply(callable $effect): self
    {
        $effect($this->value);
        // return a new instance to preserve immutability-by-convention
        return new self($this->log, $this->value);
    }

    /** bind ≡ flatMap */
    public function bind(callable $function): self
    {
        return $this->flatMap($function);
    }

    /**
     * Transform this computation's log. Tail logs appended later are unaffected.
     * Note: The constructor normalizes indices with array_values, so callers need not reindex here.
     * @param callable(array<int,mixed>):array<int,mixed> $f
     * @return self
     */
    public function censor(callable $f): self
    {
        $new = $f($this->log);
        if (!is_array($new)) {
            throw new ContractViolation('Writer::censor must return an array log');
        }
        return new self($new, $this->value);
    }

    /**
     * Monad bind: combine logs left-to-right, value becomes next's value.
     * @template B
     * @param callable(mixed):self $function
     * @return self
     */
    public function flatMap(callable $function): self
    {
        $next = $function($this->value);
        if (!$next instanceof self) {
            throw new InvalidBindReturnType('Writer::flatMap must return Writer');
        }
        return new self(array_merge($this->log, $next->log), $next->value);
    }

    /** Convenience: return the contained value (log untouched). */
    public function get(): mixed
    {
        return $this->value;
    }

    /**
     * liftA2: lift a pure (A,B)->C over two Writers, logs append L->R.
     * @param callable(mixed, mixed): mixed $f
     */
    public static function liftA2(callable $f, self $fa, self $fb): self
    {
        return new self(
            array_merge($fa->log, $fb->log),
            $f($fa->value, $fb->value)
        );
    }

    /**
     * Expose current log alongside the value, while keeping the log as-is.
     * @return self<array{0:mixed,1:array<int,mixed>}>
     */
    public function listen(): self
    {
        return new self($this->log, [$this->value, $this->log]);
    }

    /**
     * Map over the current log while keeping the original log intact.
     * Returns a Writer whose value is a pair [value, f(log)], with the log unchanged.
     *
     * This mirrors the conventional `listens` helper built on top of `listen`.
     *
     * @template B
     * @param callable(array<int,mixed>):B $f
     * @return self<array{0:mixed,1:mixed}>
     */
    public function listens(callable $f): self
    {
        return new self($this->log, [$this->value, $f($this->log)]);
    }

    /**
     * @return array<int,mixed>
     */
    public function log(): array
    {
        return $this->log;
    }

    /**
     * Functor map over value; log unchanged.
     * @template B
     * @param callable(mixed):B $f
     * @return self<B>
     */
    public function map(callable $f): self
    {
        return new self($this->log, $f($this->value));
    }

    /**
     * Lift a pure value with empty log.
     * @template T
     * @param T $value
     * @return self<T>
     */
    public static function of(mixed $value): self
    {
        return new self([], $value);
    }

    /**
     * Apply a log-transforming function contained in the value to this Writer's log.
     *
     * Conventional `pass` semantics expect the current value to be a tuple [a, g]
     * where g is a callable that transforms the log (array<int,mixed>) and returns
     * the new log (array<int,mixed>). The resulting Writer keeps value=a and
     * replaces the log with g(currentLog).
     *
     * Example:
     *   Writer::tell('x')->map(fn() => ['result', fn($log) => array_map('strtoupper', $log)])
     *         ->pass();
     *
     * @return self
     */
    public function pass(): self
    {
        $v = $this->value;
        if (!is_array($v) || count($v) !== 2) {
            throw new ContractViolation('Writer::pass expects value to be [a, callable(array):array]');
        }
        [$a, $g] = $v;
        if (!is_callable($g)) {
            throw new ContractViolation('Writer::pass expects second element to be callable');
        }
        $newLog = $g($this->log);
        if (!is_array($newLog)) {
            throw new ContractViolation('Writer::pass callable must return an array log');
        }
        return new self($newLog, $a);
    }

    /** pure ≡ of */
    public static function pure(mixed $a): self
    {
        return self::of($a);
    }

    /**
     * @return array{0:array<int,mixed>,1:mixed} [log, value]
     */
    public function run(): array
    {
        return [$this->log, $this->value];
    }

    /**
     * Append one entry to the log and carry null as value.
     * (Use map/flatMap to compute a value afterward.)
     * @param mixed $w
     * @return self<null>
     */
    public static function tell(mixed $w): self
    {
        return new self([$w], null);
    }

    /** legacy alias */
    public static function unit(mixed $a): self
    {
        return self::of($a);
    }


    public function value(): mixed
    {
        return $this->value;
    }
}