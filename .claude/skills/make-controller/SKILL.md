---
name: make-controller
description: "Use this skill when creating a controller. Triggers on: 'create a controller', 'make a controller', 'add controller', 'new controller', 'add endpoint', 'new route'. Covers: invokable final controller, FormRequest, DTO construction, dependency injection of contracts only, AppResponse usage. Never inertia() or Inertia::render()."
license: MIT
metadata:
  author: your-app
---

# Make Controller

Controllers are thin adapters. One controller per use case. Final, invokable, no business logic.

## Naming

Use-case style with `Controller` suffix:

| Use case | Controller name |
|----------|----------------|
| Register user | `RegisterUserController` |
| View item | `ViewItemController` |
| Create item | `CreateItemController` |
| Update settings | `UpdateSettingsController` |
| Delete account | `DeleteAccountController` |

## Step 1 — Create the controller

```bash
cd backend && php artisan make:controller {UseCase}Controller --module={Name} --no-interaction
```

## Step 2 — Create the FormRequest

```bash
cd backend && php artisan make:request {UseCase}Request --module={Name} --no-interaction
```

## Step 3 — Controller content

**Single-module (inject Action or Query contract directly):**
```php
<?php declare(strict_types=1);

namespace App\{Name}\Http\Controllers;

use App\{Name}\Contracts\Actions\{UseCase}Action;
use App\{Name}\Data\DomainObjects\{UseCase}DataObject;
use App\{Name}\Http\Requests\{UseCase}Request;
use App\Http\Controllers\Controller;
use App\Support\Http\AppResponse;

final class {UseCase}Controller extends Controller
{
    public function __construct(
        private {UseCase}Action $action,
    ) {}

    public function __invoke({UseCase}Request $request): AppResponse
    {
        $result = $this->action->handle(
            {UseCase}DataObject::fromRequest($request),
        );

        return app_response('{Name}/{Page}', $result->toArray());
    }
}
```

**Cross-module (inject Workflow):**
```php
<?php declare(strict_types=1);

namespace App\{Name}\Http\Controllers;

use App\Workflows\{UseCase}Workflow;
use App\{Name}\Data\DomainObjects\{UseCase}DataObject;
use App\{Name}\Http\Requests\{UseCase}Request;
use App\Http\Controllers\Controller;
use App\Support\Http\AppResponse;

final class {UseCase}Controller extends Controller
{
    public function __construct(
        private {UseCase}Workflow $workflow,
    ) {}

    public function __invoke({UseCase}Request $request): AppResponse
    {
        $result = $this->workflow->handle(
            {UseCase}DataObject::fromRequest($request),
        );

        return app_response('{Name}/{Page}', $result->toArray());
    }
}
```

## Step 4 — Register the route

In `module.routes.php`:
```php
Route::post('/{path}', {UseCase}Controller::class)
    ->middleware(['auth', 'throttle:30,1'])
    ->name('{name}.{action}');
```

Use invokable syntax — never `[Controller::class, 'method']`.

## Step 5 — Run Pint

```bash
cd backend && vendor/bin/pint --dirty --format agent
```

## Rules

- Always `final`
- Always invokable (`__invoke`)
- Never `inertia()` or `Inertia::render()` — always `app_response()`
- Never `response()->json()` — always `app_response()`
- Inject contracts, never concrete classes
- No business logic — delegate immediately to Action or Workflow
- No `Gate::authorize()` in controllers — that lives in Actions
- Middleware at the route level, not in the controller constructor
- Single-module → inject Action contract; cross-module → inject Workflow
