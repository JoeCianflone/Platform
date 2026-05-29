---
name: make-workflow
description: "Use this skill when creating a Workflow. Triggers on: 'create a workflow', 'make a workflow', 'cross-module', 'orchestrate modules', 'coordinate multiple modules'. Workflows live ONLY in App\\Workflows — never inside a module. Use only when a use case spans two or more modules."
license: MIT
metadata:
  author: your-app
---

# Make Workflow

Workflows coordinate multiple modules. They contain zero business logic — they orchestrate.

## Decision check

Before creating a Workflow, confirm the use case touches **two or more modules**.

- Touches one module → use that module's Action contract directly from the controller. No Workflow needed.
- Touches two or more modules → create a Workflow in `App\Workflows`.

## Naming

`{Process}Workflow` — describes the user-facing process:
- `RegisterUserWorkflow` (Auth + Billing modules)
- `CreateItemWorkflow` (Catalog + Moderation modules)
- `PublishItemWorkflow` (Catalog + Notification modules)

## Step 1 — Create the Workflow

```bash
cd backend && php artisan make:class app/Workflows/{Process}Workflow --no-interaction
```

Note: no `--module` flag. Workflows live in `app/Workflows/`, not inside any module.

## Step 2 — Workflow content

```php
<?php declare(strict_types=1);

namespace App\Workflows;

use App\{ModuleA}\Contracts\Actions\{ActionA}Action;
use App\{ModuleB}\Contracts\Actions\{ActionB}Action;
use App\{ModuleA}\Data\DomainObjects\{Process}DataObject;
use App\{ModuleA}\Data\DomainObjects\{Result}DataObject;
use Illuminate\Support\Facades\DB;

final class {Process}Workflow
{
    public function __construct(
        private {ActionA}Action $actionA,
        private {ActionB}Action $actionB,
    ) {}

    public function handle({Process}DataObject $data): {Result}DataObject
    {
        return DB::transaction(function () use ($data): {Result}DataObject {
            $resultA = $this->actionA->handle(/* ... */);

            $this->actionB->handle(/* map $resultA into Module B's DTO */);

            return new {Result}DataObject(/* ... */);
        });
    }
}
```

## Step 3 — Run Pint

```bash
cd backend && vendor/bin/pint --dirty --format agent
```

## Rules

- Workflows are `final` — always
- Live ONLY in `app/Workflows/` — never inside a module
- Inject Action contracts and Query contracts — never concrete classes, never models
- Wrap multi-step writes in `DB::transaction()`
- Map DTOs between modules inside the Workflow — modules don't know each other's shapes
- No business logic — if you find yourself writing an `if` based on domain rules, move it into the relevant Action
- No `Gate::authorize()` — authorization lives in Actions
- No module ever creates, owns, or exposes a Workflow
- Never instantiate with `new` — always container-resolved via `app()` or constructor injection
