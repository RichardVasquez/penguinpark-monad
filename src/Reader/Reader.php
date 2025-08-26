<?php declare(strict_types=1);

    /**
     * @codeCoverageIgnore
     */
    /**
     * Until I determine what is different from local env and CI env
     */


    namespace PenguinPark\Monad\Reader;

use Closure;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Typeclass\Apply;
use PenguinPark\Monad\Typeclass\Monad;

/**
 * @template E of mixed  Environment type
 * @template A of mixed  Result type
 */
final class Reader implements Monad
{
    /** @var Closure(E):A */
    private Closure $run;

    /**
     * @param Closure(E):A $run
     */
    private function __construct(Closure $run)
    {
        $this->run = $run;
    }

    public function __toString(): string
    {
        return 'Reader(<env -> value>)';
    }

    /**
     * Applicative apply.
     * This Reader must produce a callable; it will be applied to the value of $fa
     * under the SAME environment.
     *
     * Reader<E, callable(A):B>::ap(Reader<E, A>) -> Reader<E, B>
     *
     * @template B
     * @param Reader|Apply $fa // Reader<E, A>
     * @return self         // Reader<E, B>
     */
    public function ap(self|Apply $fa): self
    {
        $rf = $this->run;   // (E) -> callable
        $ra = $fa->run;     // (E) -> A

        /**
         * @return mixed
         * @var Closure(mixed):mixed $fn
         */
        $fn = static function ($env) use ($rf, $ra) {
            $f = $rf($env);
            if (!is_callable($f)) {
                throw new ApCallableExpected('Reader::ap expects the receiver to hold a callable');
            }
            return $f($ra($env));
        };
        return new self($fn);
    }

    /**
     * Ask for the current environment.
     * @template Env
     * @return Reader<Env, Env>
     */
    public static function ask(): self
    {
        /** @var Closure(mixed):mixed $fn */
        $fn = static fn ($env) => $env;
        return new self($fn);
    }

    /**
     * Map the environment to a derived value.
     * Convenience alias for Reader::ask()->map($f).
     *
     * @template Env
     * @template R
     * @param callable(Env):R $f
     * @return Reader<Env, R>
     */
    public static function asks(callable $f): self
    {
        /** @var Reader<Env, Env> $r */
        $r = self::ask();
        /** @var Reader<Env, R> */
        return $r->map($f);
    }

    /** bind ≡ flatMap */
    public function bind(callable $function): self
    {
        return $this->flatMap($function);
    }

    /**
     * Monad bind.
     * @template B
     * @param callable(A):Reader<E,B> $function
     * @return Reader<E, B>
     */
    public function flatMap(callable $function): self
    {
        $run = $this->run;
        /**
         * @return mixed
         * @var Closure(E):mixed $fn
         */
        $fn = static function ($env) use ($run, $function) {
            $next = $function($run($env));
            if (!$next instanceof self) {
                throw new InvalidBindReturnType('Reader::flatMap must return Reader');
            }
            return $next->run($env);
        };
        return new self($fn);
    }

    /**
     * liftA2: lift a pure (A,B)->C to Readers, evaluated in the SAME environment.
     *
     * @template A1
     * @template B
     * @template C
     * @param callable(A1,B):C $f
     * @param self $fa  // Reader<E, A1>
     * @param self $fb  // Reader<E, B>
     * @return self     // Reader<E, C>
     */
    public static function liftA2(callable $f, self $fa, self $fb): self
    {
        $ra = $fa->run;  // (E)->A
        $rb = $fb->run;  // (E)->B

        /**
         * @return mixed
         * @var Closure(mixed):mixed $fn
         */
        $fn = static fn ($env) => $f($ra($env), $rb($env));
        return new self($fn);
    }

    /**
     * Transform the environment for this computation (scoped override).
     * @param callable(E):E $f
     * @return Reader<E, A>
     */
    public function local(callable $f): self
    {
        $run = $this->run;
        /**
         * @return mixed
         * @var Closure(E):mixed $fn
         */
        $fn = static fn ($env) => $run($f($env));
        return new self($fn);
    }

    /**
     * Functor map.
     * @template B
     * @param callable(A):B $f
     * @return Reader<E, B>
     */
    public function map(callable $f): self
    {
        $run = $this->run;
        /**
         * @return mixed
         * @var Closure(E):mixed $fn
         */
        $fn = static fn ($env) => $f($run($env));
        return new self($fn);
    }

    /**
     * Lift a pure value: ignores the environment.
     * @template T
     * @param T $value
     * @return Reader<mixed, T>
     */
    public static function of(mixed $value): self
    {
        /**
         * @return mixed
         * @var Closure(mixed):mixed $fn
         */
        $fn = static fn ($env) => $value;
        return new self($fn);
    }

    /** pure ≡ of */
    public static function pure(mixed $a): self
    {
        return self::of($a);
    }

    /**
     * Construct a Reader directly from an environment-dependent function.
     * Alias for providing the underlying (Env)->A.
     *
     * @template Env
     * @template R
     * @param callable(Env):R $f
     * @return Reader<Env, R>
     */
    public static function reader(callable $f): self
    {
        // Use first-class callable to preserve by-ref captures when available
        return new self($f(...));
    }

    /**
     * Provide the environment and get the result.
     * @param E $env
     * @return A
     */
    public function run(mixed $env): mixed
    {
        $r = $this->run;
        return $r($env);
    }
}
