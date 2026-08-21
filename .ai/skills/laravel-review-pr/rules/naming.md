#### Naming

- Service method named with `get`/`list`/`all` prefix instead of `collection()` / `resource()`
- Non-camelCase method or variable
- Non-PascalCase class
- Inline comments that describe what the code does rather than why

**Boolean naming — prefix required.**
Any boolean — PHP variable, method, model attribute, database column, array key, config key, or JSON response field — must use one of these prefixes: `is_` / `has_` / `with_` / `allow_` / `allows_` / `can_` / `should_` / `needs_` / `uses_`.

In PHP code use camelCase: `$isActive`, `$hasPermission`, `isRegistered()`, `hasOutstandingBalance()`.
In columns, array keys, and JSON fields use snake_case: `is_active`, `has_paid`, `is_mandatory`, `allow_guest`.

Flag any boolean that lacks a recognised prefix (e.g. `$active`, `$paid`, `$mandatory`, column `active`, JSON key `featured`, method `registered()`).

**Date/timestamp column naming — suffix required.**
Any database column storing a datetime or timestamp value must end with `_at` (e.g. `published_at`, `approved_at`, `deleted_at`). Any column storing a date-only value (no time component) must end with `_date` (e.g. `birth_date`, `event_date`, `start_date`).

Flag any `$table->timestamp(...)`, `$table->dateTime(...)`, or `$table->softDeletesTz(...)`-style custom timestamp column whose name doesn't end in `_at`, and any `$table->date(...)` column whose name doesn't end in `_date` (e.g. `$table->timestamp('approved')`, `$table->date('event')`).

**Table naming — plural snake_case, matching the model's default.**
A new table (`Schema::create('name', ...)`) must be plural snake_case and match the name Eloquent would derive automatically from its model class (`Str::plural(Str::snake(ClassName))`) — e.g. model `TicketScanHistory` should back onto table `ticket_scan_histories`, not `ticket_scan_history`. Flag any singular or otherwise non-derived table name for a standard (non-pivot) table.

Pivot tables are the accepted exception: Laravel's convention is the two related model names, singular, alphabetical order, joined with an underscore (e.g. `coupon_ticket`, `organizer_usher`) — do not flag these for being singular.

A model setting `protected $table = '...'` purely to point at a non-conventional table name (rather than for a genuine legacy/external table) is a signal the table itself is misnamed — flag the migration, not just the model property.
