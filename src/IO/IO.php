<?php /** @noinspection PhpUnused */

declare(strict_types=1);

namespace PenguinPark\Monad\IO;

use Closure;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\BracketReturnTypeExpected;
use PenguinPark\Monad\Exception\ContractViolation;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Either\Either;
use PenguinPark\Monad\Either\Left;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Exception\UnwrapLeft;
use PenguinPark\Monad\Exception\UnwrapNothing;
use PenguinPark\Monad\Maybe\Maybe;
use PenguinPark\Monad\Typeclass\Apply;
use PenguinPark\Monad\Typeclass\Monad;
use Throwable;

/** @template A */
final class IO implements Monad
{
    /** @var Closure():mixed */
    private Closure $thunk;

    /** @param Closure():mixed $thunk */
    private function __construct(Closure $thunk)
    {
        $this->thunk = $thunk;
    }

    public function __toString(): string { return 'IO(<thunk>)'; }

    /**
     * Applicative apply: this IO must hold a callable.
     * IO<callable(A):B> -> IO<A> -> IO<B>
     */
    public function ap(self|Apply $fa): self
    {
        $tF = $this->thunk;
        $tA = $fa->thunk;
        return new self(static function () use ($tF, $tA) {
            $f = $tF();
            if (!is_callable($f)) {
                throw new ApCallableExpected('IO::ap expects the receiver to hold a callable');
            }
            return $f($tA());
        });
    }

    public function apply(callable $effect): self
    {
        return $this->tap($effect);
    }

    /**
     * @return IO
     **/
    public function attempt(): self
    {
        $t = $this->thunk;
        return new self(static function () use ($t) {
            try {
                return new Right($t());
            }
            catch (Throwable $e) {
                return new Left($e);
            }
        });
    }

    public function bind(callable $function): self
    {
        return $this->flatMap($function);
    }

    /**
     * @template R
     * @template B
     * @param IO $acquire
     * @param callable(R):IO $use
     * @param callable(R):IO $release
     * @return IO<B>
     */
    public static function bracket(self $acquire, callable $use, callable $release): self
    {
        return self::delay(static function () use ($acquire, $use, $release) {
            $res = $acquire->unsafeRun();
            try {
                $u = $use($res);
                if (!$u instanceof IO) {
                    throw new BracketReturnTypeExpected('IO::bracket use must return IO');
                }
                return $u->unsafeRun();
            } finally {
                $rel = $release($res);
                if (!$rel instanceof IO) {
                    throw new BracketReturnTypeExpected('IO::bracket release must return IO');
                }
                $rel->unsafeRun();
            }
        });
    }

    /** @template T @param callable():T $thunk
     * @return IO<T>
     */
    public static function delay(callable $thunk): self
    {
        return new self($thunk(...)); // no wrappers; preserves by-ref captures
    }

    /** @template B @param callable(mixed):IO $f
     * @param callable $function
     * @return IO
     */
    public function flatMap(callable $function): self
    {
        $t = $this->thunk;
        return new self(static function () use ($t, $function) {
            $next = $function($t());
            if (!$next instanceof IO) {
                throw new InvalidBindReturnType('IO::flatMap must return IO');
            }
            return $next->unsafeRun();
        });
    }

    /**
     * @template L
     * @template R
     * @param Either<L,R> $e
     * @return IO<R>
     */
    public static function fromEither(Either $e): self
    {
        return self::delay(static function () use ($e) {
            if ($e->isRight()) {
                return $e->getOrElse(null);
            }
            $err = $e->fold(fn($l) => $l, fn($r) => $r);
            $msg = is_string($err)
                ? $err
                : (is_scalar($err)
                    ? (string)$err
                    : 'Left error');
            throw new UnwrapLeft($msg);
        });
    }

    /**
     * @template T
     * @param Maybe $m
     * @param Throwable|string $ifNothing
     * @return IO
     */
    public static function fromMaybe(Maybe $m, Throwable|string $ifNothing = 'Nothing'): self
    {
        return self::delay(static function () use ($m, $ifNothing) {
            if ($m->isJust()) {
                return $m->getOrElse(null);
            }
            if ($ifNothing instanceof Throwable) {
                throw $ifNothing;
            }
            throw new UnwrapNothing((string)$ifNothing);
        });
    }

    /**
     * Execute the effect and return its result (alias of unsafeRun()).
     */
    public function get(): mixed
    {
        return $this->unsafeRun();
    }

    /**
     * Convenience: recover by mapping an error to a pure value, without building IO manually.
     *
     * @param callable(Throwable):mixed $handler
     * @return IO
     */
    public function handleError(callable $handler): self
    {
        $t = $this->thunk;
        return new self(static function () use ($t, $handler) {
            try {
                return $t();
            }
            catch (Throwable $e) {
                return $handler($e);
            }
        });
    }

    /**
     * Handle an error by mapping it to a fallback IO and executing that instead.
     * Leaves successful results unchanged.
     *
     * Typical usage: IO::delay(fn() => risky())->handleErrorWith(fn(Throwable $e) => IO::of($default))
     *
     * @param callable(Throwable):IO $handler  F receives the thrown Throwable and must return an IO fallback
     * @return IO
     */
    public function handleErrorWith(callable $handler): self
    {
        $t = $this->thunk;
        return new self(static function () use ($t, $handler) {
            try {
                return $t();
            }
            catch (Throwable $e) {
                $fallback = $handler($e);
                if (!$fallback instanceof self) {
                    throw new ContractViolation('IO::handleErrorWith must return IO');
                }
                // Run the fallback effect and return its result
                return $fallback->unsafeRun();
            }
        });
    }

    /**
     * liftA2: run effects left-to-right, then apply a pure (A,B)->C.
     */
    public static function liftA2(callable $f, self $fa, self $fb): self
    {
        $ta = $fa->thunk; $tb = $fb->thunk;
        return new self(static function () use ($f, $ta, $tb) {
            return $f($ta(), $tb());
        });
    }

    /** @template B @param callable(mixed):B $f
     * @return IO<B>
     */
    public function map(callable $f): self
    {
        $t = $this->thunk;
        return new self(static fn () => $f($t()));
    }

    /**
     * Map an error raised during execution to another Throwable and rethrow it.
     * Leaves successful results unchanged.
     *
     * @param callable(Throwable):Throwable $f
     * @return IO
     */
    public function mapError(callable $f): self
    {
        $t = $this->thunk;
        return new self(static function () use ($t, $f) {
            try {
                return $t();
            }
            catch (Throwable $e) {
                $mapped = $f($e);
                if (!$mapped instanceof Throwable) {
                    throw new ContractViolation('IO::mapError must throw a Throwable');
                }
                throw $mapped;
            }
        });
    }

    /** Optional: cache result to avoid re-running effects */
    public function memoize(): self
    {
        $t = $this->thunk;
        $has = false;
        $val = null;
        /** @var Throwable|null $err */
        $err = null;
        return new self(static function () use ($t, &$has, &$val, &$err) {
            if ($has) {
                return $val;
            }
            if ($err) {
                throw $err;
            }
            try {
                $val = $t(); $has = true; return $val;
            }
            catch (Throwable $e) {
                $err = $e; throw $e;
            }
        });
    }

    /** @template T @param T $value
     * @return IO<T>
     */
    public static function of(mixed $value): IO
    {
        return new self(static fn () => $value);
    }

    public static function pure(mixed $a): IO
    {
        return self::of($a);
    }

    /** @param callable(mixed):void $effect @return IO */
    public function tap(callable $effect): self
    {
        $t = $this->thunk;
        return new self(static function () use ($t, $effect) {
            $v = $t();
            $effect($v);
            return $v;
        });
    }

    /** Actually perform the effect. */
    public function unsafeRun(): mixed
    {
        return ($this->thunk)();
    }
}
