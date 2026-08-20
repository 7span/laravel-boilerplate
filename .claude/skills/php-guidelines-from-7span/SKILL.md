---
name: php-guidelines-from-7span
description: 7Span's PHP and Laravel coding standards covering PSR-12, class structure, docblocks, control flow, Laravel conventions (routes, controllers, services, models, enums, commands), validation, authorization, query performance, testing policy, and naming. Must use when reading or writing PHP code (controllers, services, models, migrations, FormRequests, Resources, commands, tests).
metadata:
    author: 7Span
    tags: php, Laravel, best practices, coding standards
---

# PHP Guidelines (7Span)

## Core Laravel Principle

**Follow Laravel conventions first.** If Laravel has a documented way to do something, use it. Only deviate when you have a clear justification.

## PHP Standards

-   Follow PSR-12 (which extends PSR-1)
-   Use snake_case for non-public-facing strings (array keys, database columns, config keys)
-   Use short nullable notation: `?string` not `string|null`
-   Always specify `void` return types when methods return nothing
-   Always import namespaces with `use` statements. Never use inline fully qualified class names (e.g. `\Exception`, `\Illuminate\Support\Facades\Http`).
-   Never use single-letter variable names. Use descriptive names (e.g. `$exception` not `$e`, `$request` not `$r`).
-   The `declare(strict_types=1)` directive is enforced automatically by Laravel Pint. Do not add it manually.
-   Add every new localization key to all of the project's language files (all configured locales); always include the English string.

## Class Structure

-   Use typed properties, not docblocks:

    ```php
    // Bad
    /** @var string */
    public $name;

    // Good
    public string $name;
    ```

-   One trait per line:

    ```php
    // Bad
    use HasFactory, SoftDeletes, Notifiable;

    // Good
    use HasFactory;
    use SoftDeletes;
    use Notifiable;
    ```

## Type Declarations & Docblocks

-   Document iterables with generics:
    ```php
    /** @return Collection<int, User> */
    public function getUsers(): Collection
    ```

### Docblock Rules

-   Don't use docblocks for fully type-hinted methods (unless description needed)
-   **Always import classnames in docblocks** - never use fully qualified names:
    ```php
    use \Spatie\Url\Url;
    /** @return Url */
    ```
-   Use one-line docblocks when possible: `/** @var string */`
-   Most common type should be first in multi-type docblocks:
    ```php
    /** @var Collection|SomeWeirdVendor\Collection */
    ```
-   If one parameter needs docblock, add docblocks for all parameters
-   For iterables, always specify key and value types:
    ```php
    /**
     * @param array<int, MyObject> $myArray
     * @param int $typedArgument
     */
    function someFunction(array $myArray, int $typedArgument) {}
    ```
-   Use array shape notation for fixed keys, put each key on it's own line:
    ```php
    /** @return array{
       first: SomeClass,
       second: SomeClass
    } */
    ```

## Control Flow

-   **Happy path last**: Handle error conditions first, success case last
-   **Avoid else**: Use early returns instead of nested conditions
-   **Separate conditions**: Prefer separate early-return guard clauses over a compound `&&`; nest only when an early return isn't possible
-   **Ternary operators**: Each part on own line unless very short

```php
// Happy path last
if (! $user) {
    return null;
}

if (! $user->isActive()) {
    return null;
}

// Process active user...

// Short ternary
$name = $isFoo ? 'foo' : 'bar';

// Multi-line ternary
$result = $object instanceof Model ?
    $object->name :
    'A default value';

// Ternary instead of else
$condition
    ? $this->doSomething()
    : $this->doSomethingElse();

// Bad: compound condition with &&
if ($user->isActive() && $user->hasPermission('edit')) {
    $user->edit();
}

// Good: guard clauses with early returns
if (! $user->isActive()) {
    return;
}

if (! $user->hasPermission('edit')) {
    return;
}

$user->edit();
```

## Laravel Conventions

### Routes

-   URLs: kebab-case, plural resource names (`/error-occurrences`)
-   Route names: snake_case (`->name('open_source')`)
-   Parameters: snake_case (`{user_id}`)
-   Use tuple notation: `[Controller::class, 'method']`
-   For API resources, register routes with `Route::apiResource(...)` (it covers `index`, `store`, `show`, `update`, `destroy` and omits `create`/`edit`); use `Route::resource(...)` only for web controllers that render forms.
-   HTTP verbs carry the action: `GET` read, `POST` create, `PUT`/`PATCH` update, `DELETE` destroy. Never put verbs in URLs (no `/getOrders`, `/create-order`, `/orders/delete`).
-   Non-CRUD actions use sub-resources: `POST /orders/{order}/refund`, not `POST /refund-order/{order}`.
-   Match status codes to semantics: `200` success, `201` create, `204` no-content delete, `403` authorization denial, `404` missing, `400`/`422` validation errors.
-   Response shape: single item → Resource, list → a resource collection (or the paginator for paginated lists). Match the URL to the method: list → `/orders`, detail → `/orders/{order}`.
-   Limit deep nesting for simplicity (`/errors/1/occurrences` over `/error-occurrences/1`)

### Controllers

-   Singular resource name + `Controller` suffix (`OrderController`, `UserController`)
-   Stick to the resource methods (`index`, `store`, `show`, `update`, `destroy`; add `create` and `edit` only for web form-render routes, not APIs)
-   Keep controllers thin: validate via a FormRequest, delegate business logic to a Service, return a Resource. No Eloquent queries or business rules in the controller.
-   For `apiResource` controllers, use `index()` for the list and `show()` for the detail. For non-resource controllers, use a concise plural noun for lists (`vendors()`) and a singular noun for detail (`vendor()`). Don't invent names like `getVendors()`, `vendorsList()`, or `listAllVendors()`.
-   Every JSON response goes through an Eloquent API Resource — single item → Resource, list → a resource collection. Don't return raw arrays or `toArray()`.
-   Extract new controllers for non-CRUD actions as a singleton controller
-   For the response use the `ApiResponser` trait to return the response.
    ```php
    // Success response
    return $this->success($resource, 200);
    // Collection response
    return UserResource::collection($users);
    // Resource response
    return $this->resource(new UserResource($user));
    ```

### Services

-   Put business logic in service classes.
-   Name service methods `collection($inputs)` for list endpoints and `resource($id)` for single-item endpoints — matching the project's other services (e.g. `UserService`, `OrderService`). Don't invent names like `thingsPaginated()`, `listThings()`, or `getAllThings()`.

### Models

-   Set casts in the `casts()` method, not the `$casts` property.
-   Type-hint every relationship method's return type (`HasMany`, `BelongsTo`, …).
-   Use `SoftDeletes` where rows should be recoverable rather than hard-deleted.

### Enums

-   Use backed enums; read the stored value with `->value` at call sites.
-   Cast model attributes to the enum via the `casts()` method.

### Configuration

-   Files: kebab-case (`pdf-generator.php`)
-   Keys: snake_case (`chrome_path`)
-   Read config with `config('app.name')` — never call `env()` outside config files.
-   Add site configurations to `config/site.php` and do not create new files. Also, include the configuration keys in the `.env.example` file.

### Artisan Commands

-   Names: kebab-case (`delete-old-records`)
-   Always provide feedback (`$this->comment('All ok!')`)
-   Show progress for loops, summary at end
-   Put output BEFORE processing item (easier debugging):

    ```php
    $this->output->title('Command started');
    $items->each(function(Item $item) {
        $this->info("Processing item id `{$item->id}`...");
        $this->processItem($item);
    });

    $this->comment("Processed {$items->count()} items.");
    $this->output->success('Command completed successfully');
    ```

-   Add the logs for the scheduled commands in the `laravel.log` file.
-   Add a dry-run option only for destructive commands; skip it otherwise.

### Exceptions

-   When return the error response use the `CustomException` exception.
    ```php
    throw new CustomException(__('message.entity.entityNotFound', ['entity' => "route $route"]));
    ```

## Strings & Formatting

-   **String interpolation** over concatenation:

    ```php
    // Bad
    $greeting = 'Hello, ' . $user->name . '! Welcome to ' . $app;

    // Good
    $greeting = "Hello, {$user->name}! Welcome to {$app}";
    ```

## Comments

Be very critical about adding comments as they often become outdated and can mislead over time. Code should be self-documenting through descriptive variable and function names.

Adding comments should never be the first tactic to make code readable.

_Instead of this:_

```php
// Get the failed checks for this site
$checks = $site->checks()->where('status', 'failed')->get();
```

_Do this:_

```php
$failedChecks = $site->checks()->where('status', 'failed')->get();
```

**Guidelines:**

-   Don't add comments that describe what the code does - make the code describe itself
-   Short, readable code doesn't need comments explaining it
-   Use descriptive variable names instead of generic names + comments
-   Only add comments when explaining _why_ something non-obvious is done, not _what_ is being done
-   Never add comments to tests - test names should be descriptive enough
-   A project's CLAUDE.md may impose a stricter comment/docblock policy (e.g. none unless explicitly requested); when it does, that overrides these defaults.

## Whitespace

-   Add blank lines between statements for readability
-   Exception: sequences of equivalent single-line operations
-   No extra empty lines between `{}` brackets
-   Let code "breathe" - avoid cramped formatting

## Validation

-   Use a FormRequest for every input-taking endpoint; never inline `$request->validate()` in a controller.
-   Keep the FormRequest `authorize()` returning `true` — handle authorization in the controller (see Authorization), not in the FormRequest.
-   Use array notation for multiple rules (easier for custom rule classes):
    ```php
    public function rules() {
        return [
            'email' => ['required', 'email'],
        ];
    }
    ```
-   Custom validation rules use snake_case:
    ```php
    Validator::extend('organisation_type', function ($attribute, $value) {
        return OrganisationType::isValid($value);
    });
    ```

## Authorization

-   Policies use camelCase: `Gate::define('editPost', ...)`
-   Use CRUD words, but `view` instead of `show`
-   Run permission checks in the controller with `$this->authorize('gate-name')` at the top of the method — NOT inside the FormRequest `authorize()` (keep that returning `true`).

## Translations

-   Use the `__()` function over the `@lang` directive: `__('messages.welcome')`.

## Testing

-   **Do NOT write tests by default.** When adding an endpoint, controller method, service, or feature, do not create or update Pest tests unless the user explicitly asks ("write tests for X", "cover this with Pest", etc.).
-   If you change code already covered by a test, you may update that test to keep it passing — but don't add new tests proactively.
-   This overrides any generic "write or update a test to verify the feature" guidance elsewhere.

## Code Readability

-   Write code a junior developer can read top-to-bottom without prior context. Prefer clarity over cleverness — no nested ternaries, no dense one-liners hiding business logic, no premature abstractions.
-   Keep methods small. If a method does more than one thing, extract the secondary step into a private helper with a descriptive name.
-   Use names that describe intent, not mechanics: `$paidTicketsInRange`, `$hasOutstandingBalance` — not `$t`, `$flag`, `$data2`.
-   For comment specifics, follow the Comments section: explain the non-obvious _why_, never restate _what_ the code does.

## Query Performance

-   Avoid N+1 by default: eager-load with `with()`, `load()`, or `withCount()` what you'll read. Never lazy-load inside loops or Resource transforms.
-   For lists, use the paginator (`->paginate($perPage)`) — never `->get()` on a query that can return unbounded rows.
-   Prefer a single aggregated query (`selectRaw(...)` + `joinSub` + `groupBy`) over per-row SUM/COUNT subqueries for stats and dashboards.
-   Add indexes for every column used in `WHERE`, `ORDER BY`, or JOIN conditions. Check the table's migration before writing the query; add a migration for missing indexes.
-   When a query returns only aggregate/alias columns, call `->toBase()->get()` so rows come back as `stdClass`.
-   Before shipping a new list/report endpoint, read the generated SQL (`->toSql()` or your query profiler) to confirm no N+1 and that indexes are used.

## Loops and Collections

-   Prefer Laravel Collection methods (`each`, `map`, `filter`, `groupBy`, `pluck`, `reduce`, `flatMap`, `keyBy`) over plain `foreach` wherever the semantics are equivalent.
-   Avoid nested loops. If nested iteration is unavoidable, use collection methods on the inner pass. A nested loop that executes a query is always wrong.
-   Never execute a query inside a loop. Use `whereIn()`, `with()`, `load()`, or a single aggregated query before the loop.

## Enums

-   Compare enum instances directly: `$model->status === Status::Active`, never `$model->status === 'active'` or `$model->status->value === 'active'`.
-   Use `Rule::enum(MyEnum::class)` in FormRequest rules — never a plain `in:` string listing enum values.
-   Use enum constants everywhere (in `where()` clauses, conditions, `match` arms). No raw string/integer literals where an enum case exists.

## FormRequest Rules

-   Every `_id` field (e.g. `event_id`, `user_id`) must include an `exists:table,id` rule.
-   `authorize()` must return `true`. Authorization belongs in the controller via `$this->authorize()`.

## Notifications and Email

-   All notifications and emails must go through a Notification class dispatched via `$user->notify()` or `Notification::send()`.
-   Never dispatch a `Mailable` directly from a controller or service.
-   Every Notification class must implement `ShouldQueue`.

## API Documentation (Dedoc Scramble)

This project uses `dedoc/scramble` for API docs. All new API-facing code must satisfy these rules so Scramble can generate accurate schemas.

### Models

-   Every model must have a complete class-level PHPDoc block with `@property` for all columns and `@property-read` for all computed attributes and relationships.
-   Every enum-backed attribute and every float/monetary field must have a `display_*` companion:
    1. `@property-read string|null $display_{field}` in the class docblock
    2. The field in `$appends`
    3. A `protected function display{Field}(): Attribute` method using `Attribute::make(get: ...)`

```php
/**
 * @property SurveyStatus $status
 * @property-read string|null $display_status
 */
class Survey extends Model
{
    protected $appends = ['display_status'];

    protected function displayStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status->label(),
        );
    }
}
```

### Resources

-   Every Resource class must have `#[SchemaName('ModelName')]` above the class declaration.
-   Every `toArray()` must have a complete `@return array{...}` PHPDoc block with exact types:
    -   Monetary/price fields: `float|null`, not `string`
    -   Enum raw value: `string|null`
    -   Display labels: `string|null`
    -   Relationships: `UserResource|null`

```php
use Dedoc\Scramble\Attributes\SchemaName;

#[SchemaName('Order')]
class Resource extends JsonResource
{
    /**
     * @return array{
     *     id: int,
     *     status: string|null,
     *     display_status: string|null,
     *     total: float|null,
     *     display_total: string|null,
     *     user: UserResource|null
     * }
     */
    public function toArray(Request $request): array
```

### Controllers

-   Document `media`, `appends`, and `filter[*]` query params with `#[QueryParameter]` above the method.
-   Document conditional/ambiguous body fields with `#[BodyParameter]` above the method.

```php
use Dedoc\Scramble\Attributes\QueryParameter;
use Dedoc\Scramble\Attributes\BodyParameter;

#[QueryParameter('media', description: 'Comma-separated media tags. Available: profile', example: 'profile')]
public function index(IndexRequest $request): AnonymousResourceCollection

#[BodyParameter('event_id', description: 'Event ID. Required when season_id is not provided.', required: false, type: 'integer', example: 1)]
public function store(StoreRequest $request): JsonResponse
```

## Code Quality

-   Run `vendor/bin/pint --dirty` before finalizing any change.
-   If PHPStan/Larastan is installed (the project has a `phpstan.neon` or `phpstan.neon.dist`), run it on the changed code before finalizing and fix every reported error. Use the project's composer script when one exists (e.g. `composer test:types`), otherwise `vendor/bin/phpstan analyse`. The rules and level configured in `phpstan.neon` are the source of truth; never lower the level, remove rules, or add ignore entries to silence an error.
-   If PHP Insights is installed, run `php artisan insights` before finalizing non-trivial changes so a regression doesn't surface only in CI, and keep all four scores at or above the project's configured floors. If a change drops a score below its floor, fix it in the same change.

## Quick Reference

### Naming Conventions

-   **Classes**: PascalCase (`UserController`, `OrderStatus`)
-   **Methods/Variables**: camelCase (`getUserName`, `$firstName`)
-   **Routes**: kebab-case (`/open-source`, `/user-profile`)
-   **Config files**: kebab-case (`pdf-generator.php`)
-   **Config keys**: snake_case (`chrome_path`)
-   **Artisan commands**: kebab-case (`php artisan delete-old-records`)

### Boolean Naming

Any boolean — whether a PHP variable, method, model attribute, database column, array key, config key, validation rule key, or JSON response field — must use one of these prefixes so its type is self-evident:

| Context | Prefix style | Examples |
|---|---|---|
| PHP variable | camelCase | `$isActive`, `$hasPermission`, `$withDiscount`, `$allowsGuests` |
| PHP method | camelCase | `isRegistered()`, `hasOutstandingBalance()`, `withTax()`, `allowsRefund()` |
| Model attribute / accessor | snake_case | `is_active`, `has_verified_email`, `with_discount`, `allows_refund` |
| Database column | snake_case | `is_active`, `has_paid`, `is_mandatory`, `allow_guest` |
| Array / config / JSON key | snake_case | `is_featured`, `has_media`, `with_vat`, `allow_resell` |

Accepted prefixes: `is_` / `has_` / `with_` / `allow_` / `allows_` / `can_` / `should_` / `needs_` / `uses_`.

Flag any boolean named without one of these prefixes (e.g. `$active`, `$paid`, `$mandatory`, `$discount`, `active`, `paid`).

### File Structure

-   Controllers: singular resource name + `Controller` (`OrderController`, `UserController`)
-   Views: camelCase (`openSource.blade.php`)
-   Jobs: action-based (`CreateUser`, `SendEmailNotification`)
-   Commands: action + `Command` suffix (`PublishScheduledPostsCommand`)
-   Mailables: purpose + `Mail` suffix (`AccountActivatedMail`)
-   Resources: a per-resource folder with a `Resource` class for single items and a `Collection` class for lists (`User/Resource.php`, `User/Collection.php`)
-   Enums: descriptive name, no prefix (`OrderStatus`, `BookingType`)

### Migrations

-   Always write both `up()` and `down()` methods so migrations can be rolled back.
-   Don't edit a migration that has already shipped; add a new migration instead.
-   When modifying a column, re-declare all of its attributes — Laravel drops any you omit.
