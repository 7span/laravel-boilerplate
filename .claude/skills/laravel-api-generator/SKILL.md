---
name: laravel-api-generator
description: Build production-grade Laravel REST APIs using opinionated architecture patterns with Laravel best practices. Use when building, scaffoling, or reviewing Laravel APIs with specifications for stateless design, versioned endpoints, invokable controllers, and PSR-12 code quality standards. Triggers on "build a Laravel API", "create Laravel endpoints", "add API authentication", "review Laravel API code", "refactor Laravel API", or "improve Laravel code quality".
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
4. **Version discipline** - Namespace-based versioning, HTTP Sunset headers

## Code Quality Standards

All code must follow Laravel best practices and PSR-12 standards:

1. **Preserve Functionality** - Refactorings change HOW code works, never WHAT it does
2. **Explicit Over Implicit** - Prefer clear, readable code over clever shortcuts
3. **Type Declarations** - Always use return types on methods, parameter types where beneficial
4. **Avoid Nested Ternaries** - Use match expressions, switch, or if/else for clarity
5. **Consistent Naming** - Follow PSR-12 and Laravel conventions strictly
6. **Proper Namespacing** - Organize imports logically, use full type hints

**When reviewing or refactoring code:**

-   Focus on clarity and maintainability over cleverness
-   Simplify complex nested logic into readable structures
-   Extract magic values into named constants or config
-   Remove unnecessary complexity while preserving exact behavior

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

class Task extends Model
{
    use BaseModel;
    use HasFactory;
    use HasUlids;
    use SoftDelete;

    protected $fillable = [
        'title',
        'description',
        'status',
        'project_id',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /* Add relationship */
    protected $relationship = [
        'project' => [
            'model' => Project::class,
        ],
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
```

### Step 2: Routes

Create resource route file at `routes/{resource}-v1.php`:

```php
use App\Http\Controllers\V1\TaskController;

Route::middleware(['auth:api'])->group(function () {
    Route::resource('/tasks', TaskController::class);
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

Create controller at `app/Http/Controllers/Api/V1/{Operation}Controller.php`:

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
use App\Http\Resources\Task\Resource as TaskResource;

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

Create resource at `app/Http/Resources/{Operation}Resource.php`:

```php
<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\Resource as UserResource;

/**
 * @property Task $resource
 */
#[SchemaName('Task')]
class Resource extends JsonResource
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

Add the `GetQB()` function into the collection method:

## Authentication Setup

Use passport for the authentication:

## Anti-Patterns to Avoid

-   Hidden query scopes
-   Breaking changes without versioning
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
