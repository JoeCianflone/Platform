---
name: arch-review
description: "Use this skill when reviewing code for architectural compliance. Triggers on: 'review this', 'check architecture', 'does this follow the rules', 'arch review', 'is this correct', 'review my code', 'check this file'. Checks: module isolation, naming conventions, final classes, DTO patterns, forbidden patterns, correct use of Workflows vs Actions, AppResponse usage."
license: MIT
metadata:
  author: your-app
---

# Architecture Review

Review the provided code against the project's architecture rules. Report each violation with file and line. Then provide the corrected version.

## Review Checklist

### Module Isolation
- [ ] No imports of another module's `Eloquent\Models`
- [ ] No imports of another module's `Data\DomainObjects`
- [ ] No imports of another module's `Domain\` internals (Actions, Queries, Scopes)
- [ ] Listeners only import `Domain\Events` from other modules (not Actions, Queries, Models, DataObjects)
- [ ] Cross-module data carried only via Snapshots inside Events

### Class Rules
- [ ] Controllers: `final`, invokable (`__invoke`), extend `App\Http\Controllers\Controller`
- [ ] Actions (concrete): `final`, extends `Action`, implements its contract
- [ ] Queries (concrete): `final`, implements its QueryContract
- [ ] Scopes: `final`, implements `{Entity}Scope`
- [ ] DataObjects: `final readonly`, implements `App\Contracts\DataObject`
- [ ] Snapshots: `final readonly`, extends `App\Support\Snapshots\Snapshot`
- [ ] ValueObjects: `final readonly`, implements `App\Contracts\ValueObject`
- [ ] Events: `final`
- [ ] Listeners: `final`
- [ ] Workflows: `final`, live only in `App\Workflows`

### Controller Rules
- [ ] No `inertia()` or `Inertia::render()` — must use `app_response()`
- [ ] No `response()->json()` — must use `app_response()`
- [ ] No business logic — delegates immediately to Action contract or Workflow
- [ ] No `Gate::authorize()` — belongs in Actions
- [ ] Injects contracts, not concrete classes

### Action Rules
- [ ] Receives DTOs, returns DTOs — never Eloquent models
- [ ] `Gate::authorize()` present where authorization is required
- [ ] Uses `DB::transaction()` for writes
- [ ] Fires events with `toSnapshot()`, not raw models

### Workflow Rules
- [ ] Lives in `App\Workflows`, never inside a module
- [ ] Injects Action contracts and Query contracts only — no concrete classes, no models
- [ ] Contains no business logic (no domain-rule `if` statements)
- [ ] Wraps multi-step writes in `DB::transaction()`

### Query Rules
- [ ] Never mutates state
- [ ] Never returns Eloquent models — always DTOs
- [ ] Uses Scope classes for all filtering (no raw `where()` in Query class)
- [ ] Uses explicit `select([...])` — no SELECT *

### Naming
- [ ] Action interface: `{Verb}{Noun}Action`
- [ ] Action concrete: `{Verb}{Noun}` (no suffix)
- [ ] Query contract: `{Entity}QueryContract`
- [ ] Query concrete: `{Entity}Query`
- [ ] Scope classes: descriptive phrase (`PublishedItems`, `WithSlug`)
- [ ] Controllers: `{UseCase}Controller`
- [ ] DataObjects: `{Concept}DataObject`
- [ ] Snapshots: `{Concept}Snapshot`

### PHP Conventions
- [ ] `declare(strict_types=1)` in every file
- [ ] Constructor property promotion used (no manual assignments)
- [ ] Explicit return types on all methods
- [ ] No empty `__construct()` unless intentionally private
- [ ] No `new WorkflowOrAction()` — container resolution only

## Output Format

For each violation:
```
{file}:{line} — {rule violated}
Fix: {what to change}
```

Then provide the corrected file(s).

## After review — run

```bash
cd backend && php artisan test --testsuite=Architecture --compact
cd backend && vendor/bin/pint --dirty --format agent
```
