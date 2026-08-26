---
name: laravel-api-generator
description: Build production-grade Laravel REST APIs using opinionated architecture patterns with Laravel best practices. Use when building, scaffoling, or reviewing Laravel APIs with specifications for stateless design, invokable controllers, and PSR-12 code quality standards. Triggers on "build a Laravel API", "create Laravel endpoints", "add API authentication", "review Laravel API code", "refactor Laravel API", or "improve Laravel code quality".
---

# Laravel API

Build Laravel REST APIs with clean, stateless, resource-scoped architecture.

## Quick Start

When user requests a Laravel API, follow this workflow:

1. **Understand requirements** - What resources? What operations? Authentication needed?
2. **Initialize project structure** - Set up routing, remove frontend bloat
3. **Build first resource** - Complete CRUD to establish pattern
4. **Add authentication** - Passport authentication
5. **Iterate on remaining resources** - Follow established pattern

## Core Architecture Principles

Read `php-guidelines-from-7span` skill for comprehensive details.
Key principles:
1. **Stateless by design** - No hidden dependencies, explicit data flow
2. **Boundary-first** - Clear separation of HTTP, business logic, data layers
3. **Resource-scoped** - Routes, controllers organized by resource

## Code Quality Standards

All code must follow `php-guidelines-from-7span`'s PSR-12 and type-safety rules. When reviewing or refactoring, use the checklist under "Code Review & Refactoring" below.

## Project Structure

```php
routes/
  api-v1.php            # Main entry point
  admin-v1.php          # All admin routes

app/Http/
  Controllers/Api/
    {Resource}Controller.php           # e.g. TaskController.php
    Admin/
      {Resource}Controller.php         # e.g. Admin/TaskController.php
  Requests/
    {Resource}/
      Store.php
      Update.php
  Resources/
    {Resource}Resource.php            # e.g. TaskResource

app/Services/
  {Resource}Service.php                # e.g. TaskService.php

app/Models/
  {Resource}.php
```

## Building a New Resource Endpoint

### Step 1: Model

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'title',
    'description',
    'status',
    'project_id',
    'created_at',
])]
class Task extends Model
{
    use BaseModel;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    /* Add relationship */
    protected $relationship = [
        'project' => [
            'model' => Project::class,
        ],
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
```

### Step 2: Routes

Add the resource's routes to `routes/api-v1.php` (the shared entry point — see Project Structure), not a new file per resource:

```php
use App\Http\Controllers\Api\TaskController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('/tasks', TaskController::class);
    Route::post('/tasks/{task}/change-status', TaskChangeStatusController::class);
});
```

### Step 3: Form Request

Create at `app/Http/Requests/{Resource}/{Operation}.php`:

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests\Tasks;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            'project_id' => ['required', 'string', 'exists:projects,id'],
        ];
    }
}
```

### Step 4: Controller

Create controller at `app/Http/Controllers/Api/{Operation}Controller.php`:

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    use ApiResponser;

    public function __construct(private TaskService $taskService) {}

    public function index(Request $request): JsonResponse
    {
        $tasks = $this->taskService->collection($request->all());

        return TaskResource::collection($tasks);
    }

    public function store(Store $request): JsonResponse
    {
        $task = $this->taskService->store($request->validated());

        return $this->success($task);
    }
}
```

## Step 5: Services

Create service at `app/Http/Services/{Operation}Service.php`:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use App\Traits\PaginationTrait;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Resources\TaskResource;

class TaskService
{
    use PaginationTrait;

    private Task $taskObj;

    public function __construct()
    {
        $this->taskObj = new Task;
    }

    public function collection(array $inputs): Collection
    {
        $tasks = $this->taskObj->getQB();

        return $this->paginationAttribute($tasks);
    }

    public function resource(int $id): Task
    {
        return $this->taskObj->findOrFail($id);
    }

    public function store(array $inputs): array
    {
        $task = $this->taskObj->create($inputs);

        $data['message'] = __('message.task_created_success');
        $data['data'] = new TaskResource($this->resource($task->id));

        return $data;
    }
}
```

## Step 6: Resource

Create resource at `app/Http/Resources/{Resource}Resource.php`:

Name it `{Model}Resource` in `App\Http\Resources` — Laravel auto-discovers this for `$model->toResource()` / `$collection->toResourceCollection()`. Only add `#[UseResource]`/`#[UseResourceCollection]` on the model when a resource doesn't follow this naming (see `php-guidelines-from-7span`).

```php
<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

/**
 * @property Task $resource
 */
#[SchemaName('Task')]
class TaskResource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Task::class;

    /**
     * @return array{
     *     id: int,
     *     user_id: int,
     *     name: string,
     *     title: string,
     *     slug: string,
     *     description: string|null,
     *     status: string,
     *     published_at: int|null,
     *     created_at: int,
     *     updated_at: int,
     *     is_published: bool,
     *     user: UserResource
     * }
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();
        $data['user'] = new UserResource($this->whenLoaded('user'));

        return $data;
    }
}
```

## Response Format

Standard format for all responses:

**Success:**

```json
{
    "data": {...},
    "meta": {...}
}
```

**Error (Problem+JSON):**

```json
{
    "message": "Error Message",
    "errors": {
        "message": "Error Message"
    }
}
```

## Query Building

List/filter endpoints go through `Spatie\QueryBuilder`, wired by the `BaseModel` trait's `getQB()` method — don't hand-roll filtering or sorting in the service.

-   `getQB()` builds a `QueryBuilder::for(static::class)` with `allowedFields`, `allowedIncludes`, `allowedFilters`, and `allowedSorts` derived from the model's fillable/queryable fields and relationships.
-   A model opts into extra behavior with optional properties: `$relationship` (includes), `$scopedFilters` / `$exactFilters` (filter types), `$defaultSort`, `$queryable` (extra filterable fields beyond fillable).
-   Call it from the service's `collection()` method, then pass the result through `PaginationTrait::paginationAttribute()`:
    ```php
    public function collection(array $inputs): LengthAwarePaginator|Collection
    {
        $tasks = $this->taskObj->getQB();

        return $this->paginationAttribute($tasks);
    }
    ```
-   If a filter or sort isn't covered, extend the model's `$scopedFilters`/`$exactFilters`/`$queryable` — don't add ad-hoc `->where()`/`->orderBy()` calls in the service.

## Rate Limiting

Every API route already gets the `throttle:api` limiter from the `api` middleware group (60 requests/minute, segmented by user ID or IP, custom JSON 429 response — see `AppServiceProvider::configureRateLimiting()`). Don't stack another blanket throttle on top. Define a new named limiter only when an endpoint needs a different budget than the default (e.g. OTP send/verify), and attach it explicitly to that route.

## Authentication Setup

Use Laravel Passport for OAuth2 API authentication — read the `passport-development` skill for grants, scopes, token lifetimes, and route protection. Don't duplicate that guidance here.

## Anti-Patterns to Avoid

-   Hidden query scopes
-   Inconsistent response formats
-   Nested ternary operators (use match expressions instead)
-   Missing type declarations on methods and parameters
-   Overly compact "clever" code that sacrifices readability

## Code Review & Refactoring

When reviewing or refactoring Laravel API code, apply these principles:

### Simplification Checklist

1. **Preserve Functionality** - Ensure refactorings don't change behavior
2. **Check Type Safety** - Add missing return types and parameter types
3. **Simplify Logic** - Replace nested ternaries with match expressions
4. **Extract Complexity** - Move complex conditions into named methods
5. **Verify Standards** - Ensure PSR-12 compliance with declare(strict_types=1)
6. **Improve Naming** - Use descriptive names that reveal intent

### Match Expression Pattern

Replace nested ternaries with match for clarity:

```php
// ❌ Avoid: Nested ternary
$status = $task->completed_at
    ? ($task->verified ? 'verified' : 'completed')
    : ($task->started_at ? 'in_progress' : 'pending');

// ✅ Prefer: Match expression
$status = match (true) {
    $task->completed_at && $task->verified => 'verified',
    $task->completed_at => 'completed',
    $task->started_at => 'in_progress',
    default => 'pending',
};
```

## Templates

Template files in `assets/templates/` for quick scaffolding:

-   Controller.php
-   FormRequest.php
-   Payload.php
-   Action.php
-   Model.php
