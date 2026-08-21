#### Migration Safety

**Missing or incomplete `down()` method.**
Flag any new migration where `down()` is empty, missing, or does not actually reverse what `up()` does (e.g. `up()` adds a column but `down()` doesn't drop it). Migrations must be rollback-safe.

**Editing an already-shipped migration instead of adding a new one.**
Flag any diff that modifies the body of an existing migration file (a file not newly added in this PR). The fix is always a new migration — never edit a migration that may have already run in another environment.

**`->change()` must re-declare every attribute the column already had.**
Laravel's `change()` drops any attribute of the column that isn't repeated in the new definition — `nullable()`, `default(...)`, `unsigned()`, length, etc. Flag any `->change()` call where the new column definition doesn't restate the column's full previous definition; read the column's original migration to confirm nothing is silently dropped.

**Missing index on a foreign key or frequently-queried column.**
Flag a new column that is a foreign key (`_id` suffix) or is used in a `where()` / `orderBy()` / join elsewhere in the diff but isn't indexed — via `->index()`, `->foreignId(...)->constrained()`, or a composite index.

**Boolean, float, and enum/status columns must have a default value.**
Flag any `$table->boolean(...)`, `$table->float(...)`/`$table->decimal(...)`, or an enum-backed status column (`$table->enum(...)` or a column backed by a PHP enum, e.g. `status`) that is left `->nullable()` with no `->default(...)`, or otherwise has no default at all. These column types must always declare a sensible default value in the migration rather than being left nullable.

**Column naming — booleans and dates.**
See `naming.md` for the full rules. Flag any `$table->boolean(...)` column without an `is_`/`has_`/etc. prefix, and any `$table->timestamp(...)`/`dateTime(...)` column without an `_at` suffix or `$table->date(...)` column without a `_date` suffix.

**Table naming — plural, matches the model.**
See `naming.md` for the full rules. Flag any `Schema::create('name', ...)` for a standard (non-pivot) table where `name` is singular or otherwise doesn't match the plural snake_case form Eloquent would derive from the corresponding model class.
