---
name: make-action
description: "Use this skill when creating an Action. Triggers on: 'create an action', 'make an action', 'add action', 'new action for', 'implement action'. Covers: the contract interface in Contracts/Actions/, the concrete class in Domain/Actions/, binding in the ServiceProvider, and the correct DTO in/out pattern. Do not use for Workflows (cross-module orchestration) or Queries (reads only)."
license: MIT
metadata:
  author: your-app
---

# Make Action

Actions are module-scoped write operations. One use case per Action. Always contract-bound.

## Naming

| Part | Pattern | Example |
|------|---------|---------|
| Interface | `{Verb}{Noun}Action` | `CreateItemAction` |
| Concrete | `{Verb}{Noun}` (no suffix) | `CreateItem` |
| DataObject in | `{Verb}{Noun}DataObject` | `CreateItemDataObject` |
| DataObject out | `{Noun}DataObject` | `ItemDataObject` |

## Step 1 — Create the contract

```bash
cd backend && php artisan make:interface Contracts/Actions/{Verb}{Noun}Action --module={Name} --no-interaction
```

Interface content:
```php
<?php declare(strict_types=1);

namespace App\{Name}\Contracts\Actions;

use App\{Name}\Data\DomainObjects\{Noun}DataObject;
use App\{Name}\Data\DomainObjects\{Verb}{Noun}DataObject;

interface {Verb}{Noun}Action
{
    public function handle({Verb}{Noun}DataObject $data): {Noun}DataObject;
}
```

## Step 2 — Create the DataObject(s)

```bash
cd backend && php artisan make:dataobject {Verb}{Noun} --module={Name} --no-interaction
cd backend && php artisan make:dataobject {Noun} --module={Name} --no-interaction
```

## Step 3 — Create the concrete action

```bash
cd backend && php artisan make:class Domain/Actions/{Verb}{Noun} --module={Name} --no-interaction
```

Concrete content:
```php
<?php declare(strict_types=1);

namespace App\{Name}\Domain\Actions;

use App\{Name}\Contracts\Actions\{Verb}{Noun}Action;
use App\{Name}\Data\DomainObjects\{Noun}DataObject;
use App\{Name}\Data\DomainObjects\{Verb}{Noun}DataObject;
use App\Support\Actions\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class {Verb}{Noun} extends Action implements {Verb}{Noun}Action
{
    public function handle({Verb}{Noun}DataObject $data): {Noun}DataObject
    {
        Gate::authorize('{policy-ability}');

        return DB::transaction(function () use ($data): {Noun}DataObject {
            // business logic here
            // fire events with snapshots: event(new ItemCreated($result->toSnapshot()));
            // return DTO, never an Eloquent model
        });
    }
}
```

## Step 4 — Bind in ServiceProvider

In `{Name}ServiceProvider::register()`:
```php
$this->app->bind(
    \App\{Name}\Contracts\Actions\{Verb}{Noun}Action::class,
    \App\{Name}\Domain\Actions\{Verb}{Noun}::class,
);
```

## Step 5 — Run Pint

```bash
cd backend && vendor/bin/pint --dirty --format agent
```

## Rules

- Actions are `final` — always
- Receive DTOs, return DTOs — never Eloquent models
- `Gate::authorize()` lives in the Action, not the controller
- Use `DB::transaction()` for writes
- Fire events with `toSnapshot()` snapshots, not models
- Never instantiate with `new` — always container-resolved
- Single-module use case: inject Action contract directly into controller
- Cross-module use case: Action is called from a Workflow, not a controller
