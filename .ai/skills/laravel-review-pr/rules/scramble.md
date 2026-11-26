#### Scramble / API Documentation (Dedoc Scramble)

This project uses `dedoc/scramble` for API documentation. All new API-facing code must be documented so Scramble can generate accurate docs.

**Models — `@property` docblocks**

Every model must have a complete class-level PHPDoc block listing all database columns and computed attributes (`@property int $id`, `@property-read HasMany $questions`, etc.). Flag when a new property, accessor, or relationship is added to the model but not reflected in the `@property` or `@property-read` docblock.

**Models — `display_*` accessors for enums and floats**

Every enum-backed attribute and every monetary/float field must have a human-readable `display_*` companion: a `@property-read string|null $display_{field}` line in the class docblock, the field name in `$appends`, and a `protected function display{Field}(): Attribute` method (e.g. `get: fn () => $this->status->label()` for enums, `get: fn (): string => number_format((float) $this->total, 2)` for floats).

Flag when:
- A new enum attribute is added without a `display_{field}` accessor + appends entry + docblock line
- A new float/monetary field is added without a `display_{field}` formatted accessor
- The accessor is defined but not listed in `$appends` or the docblock

**Resources — `#[SchemaName]` attribute**

Every Eloquent Resource class must have `#[SchemaName('ModelName')]` above the class declaration. Flag when a new Resource is added without it.

**Resources — typed `@return array{...}` on `toArray()`**

Every Resource `toArray()` must have a complete `@return array{...}` PHPDoc listing every key with its exact type (e.g. `@return array{id: int, total: float|null, display_total: string|null}`). Flag when:
- A new field is added to `toArray()` but not in the docblock
- A monetary field is typed as `string` or `int` instead of `float`
- The docblock is missing entirely

**Controllers — `#[QueryParameter]` and `#[BodyParameter]`**

Flag when a controller method accepts `media` or `appends` query params without a `#[QueryParameter('name', description: ..., example: ...)]` attribute above the method, or when ambiguous/conditional body fields are not covered by a matching `#[BodyParameter(...)]`.

Do not flag missing `#[QueryParameter(...)]` for `filter[*]` params. Filtering is handled by the model's existing `$scopedFilters`/`$exactFilters` mechanism and already works without a matching attribute — documenting it is a nice-to-have, not a convention violation, and this project's controllers are inconsistent about it already. Only `media` and `appends` are worth flagging.
