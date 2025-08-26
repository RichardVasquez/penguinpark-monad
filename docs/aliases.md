# Aliases Across Ecosystems

# Aliases Across Ecosystems

I went down a rabbit hole pulling names from half a dozen FP ecosystems. Everyone agreed on the ideas, then promptly disagreed on the **names**. I’ve already forgotten half of it the moment I coded the adapters, so this page is my future self’s memory prosthetic — a tiny Rosetta Stone for “oh right, what do they call `flatMap` over there again?”.

Slightly exasperated? Yes. But I’m happy we’ve got a clean, usable mapping now. I’ll keep adding rows as the library grows (Kleisli helpers, more traversals, etc.) and as we touch more languages.

(Only ecosystems that expose the concept under that name — or a clear, direct equivalent — are listed. We skip concepts that aren’t directly present under that name.)


---

## Constructors — `of` / `pure`

- **Haskell:** `pure`  
  _Example:_ `pure 42 :: Maybe Int`
- **Scala (Cats):** `Applicative.pure`  
  _Example:_ `Applicative[Option].pure(42)`
- **JavaScript (Fantasy Land):** `of`  
  _Example:_ `Option.of(42)`
- **Kotlin (Arrow):** `Applicative.just` (or data ctor)  
  _Example:_ `Option.just(42)` / `Some(42)`
- **Scala (std):** data ctor  
  _Example:_ `Some(42)`
- **Rust (std):** data ctor  
  _Example:_ `Some(42)` / `Ok(42)`
- **Swift (std):** data ctor  
  _Example:_ `Optional.some(42)` / `Result.success(42)`
- **F# (std):** data ctor  
  _Example:_ `Some 42` / `Ok 42`

---

## Functor — `map`

- **Haskell:** `fmap`  
  _Example:_ `fmap (+1) (Just 41)`
- **Scala (std):** `map`  
  _Example:_ `Some(41).map(_ + 1)`
- **Scala (Cats):** `map`  
  _Example:_ `Option(41).map(_ + 1)`
- **JavaScript (Fantasy Land):** `map`  
  _Example:_ `Option.of(41).map(x => x + 1)`
- **Kotlin (Arrow):** `map`  
  _Example:_ `Some(41).map { it + 1 }`
- **Elm:** `Maybe.map`, `Result.map`  
  _Example:_ `Maybe.map (\x -> x + 1) (Just 41)`
- **Rust (std):** `map`  
  _Example:_ `Some(41).map(|x| x + 1)`
- **Swift (std):** `map`  
  _Example:_ `Optional(41).map { $0 + 1 }`
- **F# (std):** `Option.map`, `Result.map`  
  _Example:_ `Option.map ((+) 1) (Some 41)`

---

## Apply / Applicative — `ap`

- **Haskell:** `<*>`  
  _Example:_ `Just (+1) <*> Just 41`
- **Scala (Cats):** `ap`  
  _Example:_ `Option(41).ap(Option((x: Int) => x + 1))`
- **JavaScript (Fantasy Land):** `ap`  
  _Example:_ `Option.of(41).ap(Option.of(x => x + 1))`
- **Kotlin (Arrow):** `ap`  
  _Example:_ `Option(41).ap(Option { f: (Int) -> Int -> f(41) })`

---

## Monad (bind) — `flatMap` / `bind`

- **Haskell:** `>>=`  
  _Example:_ `Just 41 >>= (\x -> Just (x + 1))`
- **Scala (std):** `flatMap`  
  _Example:_ `Some(41).flatMap(x => Some(x + 1))`
- **Scala (Cats):** `flatMap`  
  _Example:_ `Option(41).flatMap(x => Option(x + 1))`
- **JavaScript (Fantasy Land):** `chain`  
  _Example:_ `Option.of(41).chain(x => Option.of(x + 1))`
- **Kotlin (Arrow):** `flatMap`  
  _Example:_ `Some(41).flatMap { Some(it + 1) }`
- **Elm:** `andThen` (on `Maybe`, `Result`)  
  _Example:_ `Just 41 |> Maybe.andThen (\x -> Just (x + 1))`
- **Rust (std):** `and_then`  
  _Example:_ `Some(41).and_then(|x| Some(x + 1))`
- **Swift (std):** `flatMap`  
  _Example:_ `Optional(41).flatMap { .some($0 + 1) }`
- **F# (std):** `Option.bind`, `Result.bind`  
  _Example:_ `Option.bind (fun x -> Some (x + 1)) (Some 41)`

---

## Choice / default — `getOrElse`

- **Scala (std):** `getOrElse`  
  _Example:_ `None.getOrElse(0)`
- **Scala (Cats):** `getOrElse`  
  _Example:_ `Option.empty[Int].getOrElse(0)`
- **JavaScript (common FP libs):** `getOrElse`  
  _Example:_ `Option.empty().getOrElse(0)`
- **Elm:** `Maybe.withDefault`, `Result.withDefault`  
  _Example:_ `Maybe.withDefault 0 Nothing`
- **Rust (std):** `unwrap_or`  
  _Example:_ `None.unwrap_or(0)` / `Err(e).unwrap_or(0)`
- **Swift (std):** `??`  
  _Example:_ `nil ?? 0`
- **F# (std):** `Option.defaultValue`, `Result.defaultValue`  
  _Example:_ `Option.defaultValue 0 None`

---

## Either-specific — `mapLeft`

- **Haskell:** `bimap` / `first`  
  _Example:_ `bimap (+1) id (Left 2)`  *(maps Left)*
- **Scala (Cats):** `leftMap`  
  _Example:_ `Left(2).leftMap(_ + 1)`
- **Scala (std 2.13+):** `left.map`  
  _Example:_ `Left(2).left.map(_ + 1)`
- **JavaScript (FP libs):** `leftMap` / `bimap`  
  _Example:_ `Left(2).leftMap(x => x + 1)`
- **Kotlin (Arrow):** `mapLeft`  
  _Example:_ `Either.Left(2).mapLeft { it + 1 }`
- **Elm:** `Result.mapError`  
  _Example:_ `Result.mapError ((+) 1) (Err 2)`
- **Rust (std):** `map_err`  
  _Example:_ `Err(2).map_err(|x| x + 1)`
- **Swift (std):** `Result.mapError`  
  _Example:_ `Result<Int, Int>.failure(2).mapError { $0 + 1 }`
- **F# (std):** `Result.mapError`  
  _Example:_ `Error 2 |> Result.mapError ((+) 1)`

---

## Traversal — `traverse` / `sequence`

- **Haskell:** `traverse`, `sequence`  
  _Example:_ `traverse readMaybe ["1","2"]`
- **Scala (Cats):** `traverse`, `sequence`  
  _Example:_ `List(Option(1), Option(2)).sequence`
- **JavaScript (Fantasy Land):** `traverse`, `sequence`  
  _Example:_ `[Maybe.of(1), Maybe.of(2)].sequence(Maybe.of)`
- **Kotlin (Arrow):** `traverse`, `sequence`  
  _Example:_ `listOf(Some(1), Some(2)).sequence(Option.applicative())`

---

## IO (error/cleanup) — names used in this library

- **`handleErrorWith`**
    - **Haskell (exceptions libraries):** `handle` / `catch`
    - **Scala (Cats Effect):** `handleErrorWith`

- **`bracket`**
    - **Haskell:** `bracket`
    - **Scala (Cats Effect):** `bracket` / `Resource`

---

## Links

### Language intros
- **Haskell — A Gentle Introduction to Haskell:** https://www.haskell.org/tutorial/
- **Scala — Tour of Scala:** https://docs.scala-lang.org/tour/tour-of-scala.html
- **JavaScript — MDN JavaScript Guide (Intro):** https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Introduction
- **Kotlin — Kotlin Docs (Home/Start):** https://kotlinlang.org/docs/home.html
- **Elm — An Introduction to Elm:** https://guide.elm-lang.org/
- **F# — Microsoft Learn: F# docs:** https://learn.microsoft.com/en-us/dotnet/fsharp/

