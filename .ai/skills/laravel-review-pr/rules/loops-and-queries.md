#### Loops, Collections, and Query Efficiency

**MANDATORY — N+1 queries are never allowed.**
Every review must explicitly check for N+1 queries; this is not optional or best-effort. Flag any Eloquent call (`->find()`, `->where()->first()`, `->save()`, `->create()`, etc.) or relationship access (including implicit lazy-loaded relations like `$item->relation->field`) inside a `foreach`, `for`, `while`, `each()`, `map()`, or a Resource `toArray()` that is called per-item. Always report this as 🔴 **Error**, never as a Warning or Suggestion. The fix is eager loading with `with()` / `load()` / `loadMissing()` / `withCount()` before the loop.

**MANDATORY — duplicate queries are never allowed.**
Every review must explicitly check for duplicate queries; this is not optional or best-effort. Flag any of the following:
- The same Eloquent query (same model, same or equivalent `where`/`find` conditions) executed more than once within the same request/method instead of being fetched once and reused.
- The same relationship loaded more than once for the same model instance (e.g. `$order->load('items')` called again later in the same flow when it was already eager-loaded or already accessed).
- A query re-run inside a loop that could instead be a single `whereIn()` / batched query outside the loop (e.g. calling `Model::find($id)` per iteration instead of `Model::whereIn('id', $ids)->get()` once before the loop).

Always report duplicate queries as 🔴 **Error**.

**No nested loops.**
Flag any nested `foreach`/`for`/`while` loop. If nested iteration is unavoidable, it must use collection methods on the inner pass (e.g. `->each(fn ($item) => $item->children->each(...))`). A nested loop that executes a query inside is always an error.

**Prefer collection methods over loops.**
Use Laravel Collection methods (`each`, `map`, `filter`, `groupBy`, `pluck`, `reduce`, `flatMap`, `keyBy`, etc.) instead of `foreach` wherever the semantics are equivalent. Flag a plain `foreach` that builds an array or processes items when a collection method would express the same thing more clearly.

**Reduce total queries.**
Flag patterns where a series of queries could be collapsed: multiple `->count()` / `->sum()` calls that could become one `selectRaw` with `groupBy`, per-row subqueries that could be a single join, or sequential `find()` calls that could be a single `whereIn()`.

**Prefer `->toBase()->get()` for aggregate-only queries.**
When a query selects only computed/alias columns with no model hydration needed, flag missing `->toBase()` call.

**Use `->chunkById()` over `->chunk()` for large dataset processing.**
`->chunk()` uses `OFFSET`-based pagination — if rows are deleted or inserted during iteration, records shift and some are silently skipped. `->chunkById()` paginates by primary key and is immune to this. Flag any `->chunk()` call on a table that could have rows modified mid-iteration.
