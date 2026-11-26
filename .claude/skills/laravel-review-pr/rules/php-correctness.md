#### PHP Correctness

- Missing return type declaration on a method
- Missing parameter type hint
- Untyped class property (use typed properties, not `@var` docblocks)
- Multiple traits on one `use` line — each must be on its own line
- Inline fully qualified class name instead of a `use` import
- Single-letter variable name (`$e`, `$r`, `$t`)
- `else` used where an early return would suffice (happy path last)
- Compound `&&` condition where separate guard clauses would be clearer
- Nested ternary expression
- `switch` statement where a `match` expression would be equivalent — `match` is strict, exhaustive, and expression-based (PHP 8.0+)
- `strpos()`, `strstr()`, or `substr()` used purely to check string existence, prefix, or suffix — use `str_contains()`, `str_starts_with()`, or `str_ends_with()` instead (PHP 8.0+)
- Class property that is only ever assigned in the constructor and never mutated — should be `readonly` (PHP 8.1+); applies especially to promoted constructor parameters in DTOs and value objects
- Method that overrides a parent class or interface method without a `#[Override]` attribute — the attribute makes the override explicit and causes a compile error if the parent method is removed or renamed (PHP 8.3)
