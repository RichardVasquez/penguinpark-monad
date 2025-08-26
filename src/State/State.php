<?php declare(strict_types=1);

    /**
     * @codeCoverageIgnore
     */
    /**
     * Until I determine what is different from local env and CI env
     */


    namespace PenguinPark\Monad\State;

use Closure;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Typeclass\Apply;
use PenguinPark\Monad\Typeclass\Monad;
use SplDoublyLinkedList;

/**
 * @template S of mixed  State type
 * @template A of mixed  Result type
 *
 * Represents a pure transition S -> (S, A).
 */
final class State implements Monad
{
    /** @var Closure(S):array{0:S,1:A} */
    private Closure $step;

    /** @var array<int, callable(mixed):self>  list of binds appended via flatMap */
    private array $binds;

    /**
     * @param Closure(S):array{0:S,1:A} $step
     * @param array<int, callable(mixed):self> $binds
     */
    private function __construct(Closure $step, array $binds = [])
    {
        $this->step  = $step;
        $this->binds = $binds;
    }

    public function __toString(): string
    {
        return 'State(<S => (S,A)>)';
    }

    /**
     * Applicative ap.
     * This State must produce a callable; it will be applied to $fa's value,
     * threading the SAME state left-to-right.
     *
     * State<S, callable(A):B>::ap(State<S, A>) -> State<S, B>
     */
    public function ap(self|Apply $fa): self
    {
        return $this->flatMap(static function ($f) use ($fa): self {
            if (!is_callable($f)) {
                throw new ApCallableExpected('State::ap expects the receiver to hold a callable');
            }
            return $fa->map($f);
        });
    }

    /**
     * apply: run side-effect with the current value; keep state & value.
     * Lazy until run().
     * @param callable(mixed):void $effect
     * @return self
     */
    public function apply(callable $effect): self
    {
        return $this->map(static function ($a) use ($effect) {
            $effect($a);
            return $a;
        });
    }

    /** bind ≡ flatMap */
    public function bind(callable $function): self
    {
        return $this->flatMap($function);
    }

    /**
     * @param S $s
     * @return A
     */
    public function eval(mixed $s): mixed
    {
        [, $a] = $this->run($s);
        return $a;
    }

    /**
     * @param S $s
     * @return S
     */
    public function exec(mixed $s): mixed
    {
        [$s1, ] = $this->run($s);
        return $s1;
    }

    /**
     * @template B
     * @param callable(A):self<S,B> $function
     * @return self<S,B>
     */
    public function flatMap(callable $function): self
    {
        $binds = $this->binds;
        $binds[] = $function;
        return new self($this->step, $binds);
    }

    /** @return self<S,S> */
    public static function get(): self
    {
        $step = static fn ($s) => [$s, $s];
        return new self($step);
    }

    /**
     * @template B
     * @param callable(S):B $f
     * @return self<S,B>
     */
    public static function gets(callable $f): self
    {
        $step = static fn ($s) => [$s, $f($s)];
        return new self($step);
    }

    /**
     * Alias for get(): return the current state as the value, without changing it.
     *
     * @template T
     * @return self<T, T>
     */
    public static function getState(): self
    {
        /** @var self<mixed, mixed> */
        return self::get();
    }

    /**
     * liftA2: lift a pure (A,B)->C over two State computations.
     * Runs left-to-right, threading the same state.
     */
    public static function liftA2(callable $f, self $fa, self $fb): self
    {
        return $fa->flatMap(static fn ($a): self =>
        $fb->map(static fn ($b) => $f($a, $b))
        );
    }

    /**
     * @template B
     * @param callable(A):B $f
     * @return self<S,B>
     */
    public function map(callable $f): self
    {
        // map = flatMap(of ∘ f), implemented by appending a bind
        $binds = $this->binds;
        $binds[] = static function ($a) use ($f): self {
            /** @var self<mixed,mixed> */
            return self::of($f($a));
        };
        return new self($this->step, $binds);
    }

    /**
     * @param callable(S):S $f
     * @return self<S,null>
     */
    public static function modify(callable $f): self
    {
        $step = static fn ($s) => [$f($s), null];
        return new self($step);
    }

    /**
     * @template T
     * @param T $value
     * @return self<mixed,T>
     */
    public static function of(mixed $value): self
    {
        $step = static fn ($s) => [$s, $value];
        return new self($step);
    }

    /** pure ≡ of */
    public static function pure(mixed $a): self
    {
        return self::of($a);
    }

    /**
     * @param S $s
     * @return self<S,null>
     */
    public static function put(mixed $s): self
    {
        $step = static fn ($_old) => [$s, null];
        return new self($step);
    }

    /**
     * Execute the state machine.
     * @param S $s
     * @return array{0:S,1:A}
     */
    public function run(mixed $s): array
    {
        // First step
        $step = $this->step;
        [$state, $value] = $step($s);

        // Process remaining binds with a deque (front -> back)
        $q = new SplDoublyLinkedList();

        foreach ($this->binds as $b) {
            $q->push($b); // tail
        }

        while (!$q->isEmpty()) {
            /** @var callable(mixed):self $k */
            $k = $q->shift(); // pop from head

            $next = $k($value);
            if (!$next instanceof self) {
                throw new InvalidBindReturnType('State::run must return State');
            }

            // Execute child's step
            $nextStep = $next->step;
            [$state, $value] = $nextStep($state);

            // Prepend child's binds so they run before the remaining tail.
            // Unshift in reverse to preserve their left-to-right order.
            if (!empty($next->binds)) {
                for ($i = count($next->binds) - 1; $i >= 0; $i--) {
                    $q->unshift($next->binds[$i]);
                }
            }
        }

        /** @var array{0:S,1:A} */
        return [$state, $value];
    }

    /**
     * Alias for put($s): set the state to the provided value and yield null.
     *
     * @template T
     * @param T $s
     * @return self<T, null>
     */
    public static function setState(mixed $s): self
    {
        return self::put($s);
    }
}
