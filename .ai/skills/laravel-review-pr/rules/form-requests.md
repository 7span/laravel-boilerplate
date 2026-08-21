#### FormRequest Validation

**`_id` fields must have `exists:` rule.**
Any field ending in `_id` (e.g. `event_id`, `user_id`, `season_id`) in a FormRequest must include an `exists:table,id` validation rule. Flag missing `exists:` rules on foreign key fields.

**Enum fields must use `Rule::enum()`.**
See enums.md — same rule applies here in the `rules()` method.

**`authorize()` must return `true`.**
Authorization belongs in the controller via `$this->authorize()`, not in the FormRequest. Flag any FormRequest `authorize()` that does anything other than `return true`.
