# Monad Library Overview

This src directory contains the core monad implementations used throughout the library. Each subdirectory groups a related set of types and utilities. See each group’s README for details and examples.

- [Either](./Either/README.md) - Sum type for computations that may fail. Right is success, Left is error. Great for error handling and branching logic.
- [Maybe](./Maybe/README.md) - Optional values (Just | Nothing). Eliminates null checks with a safe API.
- [IO](./IO/README.md) - Lazy, referentially transparent effects. Describe effects now, run later with unsafeRun()/get().
- [ListM](./ListM/README.md) - Persistent list functor/applicative/monad with rich sequence operations.
- [Reader](./Reader/README.md) - Environment-based computations (dependency injection as data). Wraps functions Env -> A.
- [State](./State/README.md) - Pure stateful computations threading state S through results A.
- [Validation](./Validation/README.md) - Applicative validation that accumulates all errors. Use for form validation and similar scenarios.
- [Writer](./Writer/README.md) - Values paired with a log (array monoid). Accumulate logs alongside results.

Interoperability
- These monads are designed to compose:
  - Maybe/Either convert to each other.
  - IO can be built from Either/Maybe values at boundaries.
  - ListM provides traverse/sequence helpers for Maybe and Either.
  - Reader/State/Writer can be combined to model environment, state, and logging.

For full API references, also see the docs directory:
- [Docs](./../docs/)