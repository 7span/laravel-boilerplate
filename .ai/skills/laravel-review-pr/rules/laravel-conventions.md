#### Laravel Conventions

- `env()` called outside a config file — must use `config('key')` instead
- `DB::` used instead of `Model::query()`
- `$request->validate()` inline in a controller — must use a FormRequest
- Eloquent relationship method missing return type hint (`HasMany`, `BelongsTo`, etc.)
- Casts set via `$casts` property instead of `casts()` method
- `->get()` on a query that can return unbounded rows — should be `->paginate()`
- Raw array returned from a controller instead of going through an Eloquent Resource
- `CustomException` not used for error responses
- HTTP verb in a URL (`/getOrders`, `/create-order`)
- Wrong HTTP status code (e.g. 200 on create, 200 on delete with no body)

- Single-action controller with a named method (e.g. `index`, `store`) instead of `__invoke()` — controllers that handle exactly one action must be invokable (`__invoke()`) per project convention
- Conditional query built with an `if` block (`if ($x) { $query->where(...); }`) instead of `$query->when($x, fn ($q) => $q->where(...))` — use `->when()` / `->unless()` to keep the query chain fluent
- Loop with `->updateOrCreate()` per item for bulk operations — use `->upsert()` to collapse into a single query

**Model scope reuse — active check required.**
When added lines in a service or controller contain raw Eloquent query conditions (`->where(...)`, `->whereIn(...)`, `->whereHas(...)`, etc.), read the model class that the query targets and grep for `scope` methods. If a scope already encapsulates the same condition, flag the service/controller code for using the raw condition instead of calling the scope. Do not flag if no matching scope exists in the model.
