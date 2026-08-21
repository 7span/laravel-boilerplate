#### Enums

**Compare enum instances, not raw values.**
Flag comparisons like `$model->status === 'active'` or `$model->status->value === 'active'`. The correct form is `$model->status === Status::Active` (or `$model->status->is(Status::Active)` when the enum uses that helper).

**FormRequests must validate enums using `Rule::enum()`.**
Flag when a request field corresponds to a backed enum but the validation rule uses a plain `in:value1,value2` string instead of `Rule::enum(MyEnum::class)`. The enum class is the single source of truth for allowed values.

**Use enum constants everywhere.**
Flag raw string/integer literals where an existing enum case could be used (in `where()` clauses, array literals, condition checks, `match` arms).
