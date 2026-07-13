# Claude Code Context

This file is read automatically by Claude Code on startup. It is the single source of truth for architecture, conventions, and patterns. Follow these strictly.

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Laravel Boost Tools](#laravel-boost-tools)
3. [Core Principles](#core-principles)
4. [Backend Architecture](#backend-architecture)
5. [Decision Table](#decision-table)
6. [Mandatory Patterns](#mandatory-patterns)
7. [Forbidden Patterns](#forbidden-patterns)
8. [PHP Conventions](#php-conventions)
9. [Frontend Architecture](#frontend-architecture)
10. [Testing](#testing)
11. [Key Commands](#key-commands)
12. [Rate Limiting](#rate-limiting)
13. [Module Responsibilities](#module-responsibilities)
14. [NativePHP](#nativephp)

---

## Project Overview

A modular Laravel 13 + Inertia 3 + Vue 3 + TypeScript application. Runs as a NativePHP app (optional) and web app. Optionally gates premium features behind Stripe subscriptions.

**Package versions:**
- PHP 8.4
- Laravel 13
- Inertia Laravel v3
- Pest v4 / PHPUnit v12
- Laravel Pint v1
- Laravel Pennant
- Laravel Cashier + Stripe

**Monorepo layout:**
```
your-app/
├── .mcp.json         # Laravel Boost MCP config
├── CLAUDE.md         # This file
├── backend/          # Laravel 13
├── frontend/         # Vue 3 + TypeScript + WebAwesome
├── public/           # Laravel public dir
├── Taskfile.yml
└── pnpm-workspace.yaml
```

---

## Laravel Boost Tools

Laravel Boost is an MCP server with tools designed specifically for this application. **Prefer Boost tools over manual alternatives.**

### Always search docs before making changes

Use `search-docs` before any code changes. Do not skip this step — it returns version-specific docs based on installed packages automatically.

```
// Scope by package when you know which is relevant
packages: ['inertia-laravel', 'laravel']

// Use multiple broad topic queries
queries: ['rate limiting', 'routing rate limiting', 'routing']
```

Search syntax:
- Words = auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit"
- `"quoted phrases"` = exact position matching
- Multiple queries = OR logic

### Other Boost tools

- `database-query` — run read-only queries against the database. Use instead of tinker for inspection.
- `database-schema` — inspect table structure before writing migrations or models.
- `get-absolute-url` — resolve correct scheme, domain, and port. Always use before sharing a URL.
- `browser-logs` — read browser console logs, errors, exceptions. Only recent logs are useful.

---

## Core Principles

The system is a:

- modular monolith (backend)
- contract-driven application
- use-case-oriented architecture
- scope-driven query system
- frontend/backend split application

It intentionally avoids:

- resource-centric design
- implicit coupling
- framework-driven structure
- shared mutable state
- uncontrolled Eloquent access
- hidden cross-module dependencies

### Non-Negotiable Rules

- Modules own their internals
- Contracts define all public capabilities
- Cross-module communication ONLY via Contracts, Snapshots, or Events
- DTOs are immutable
- Snapshots are read-only cross-module facts
- Queries never mutate state
- Actions never return models
- Controllers are thin adapters only
- Workflows orchestrate cross-module processes — they live ONLY in `App\Workflows`
- Modules NEVER expose Workflows
- All dependency resolution uses the Laravel container — never `new WorkflowOrAction()`
- Enforcement is mechanical (PHPStan + Pest + ESLint)
- Prefer composition over inheritance — use traits to share behavior, not abstract base classes; or call shared utilities directly

---

## Backend Architecture

### The Golden Rule

**Modules never import models from other modules.** Cross-module communication happens exclusively via Events or Snapshots. If you find yourself importing an Eloquent model from another module, stop — use a Snapshot instead.

### High-Level Request Flow

```
Client Request
    ↓
Controller (thin adapter, final)
    ↓
FormRequest → DTO
    ↓
Workflow (cross-module) OR Action Contract (single-module)
    ↓
  [Workflow] Module Action Contracts → Internal Actions / Queries
  [Action]   Internal Actions / Queries
    ↓
Persistence
    ↓
DTO returned
    ↓
Snapshot (at boundary)
    ↓
Controller Response / Event
```

### Namespaces

```
App\Workflows     →  backend/app/Workflows/
App\Support       →  backend/app/Support/
App\Providers     →  backend/app/Providers/

App\{ModuleA}\    →  backend/modules/{ModuleA}/src/
App\{ModuleB}\    →  backend/modules/{ModuleB}/src/
```

PSR-4 per module: `App\{Name}\` → `modules/{Name}/src/`

### Directory Structure

All Laravel application code lives in `backend/`. Everything outside `backend/modules/` is shared infrastructure.

```
backend/
├── app/
│   ├── Workflows/                # Cross-module orchestration ONLY
│   │── Actions/              # Base Action class
│   │── DataObjects/            # Base Data class
│   │── Snapshots/            # Base Snapshot class
│   │── ValueObjects/         # Base ValueObject class
│   │── Http/
│   │   └── AppResponse.php   # Always use this, never bare inertia()
│   └── Providers/
│       ├── ModuleServiceProvider.php
│       └── FeatureServiceProvider.php
└── modules/
    # your modules here
```

### Every Module Contains

The canonical structure is defined in `backend/module-structure.yml` — that file is used by `php artisan make:module` and is always authoritative. Key directories:

```
modules/{Name}/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── tests/
│   ├── Unit/
│   └── Feature/
└── src/
    ├── {Name}ServiceProvider.php
    ├── module.routes.php
    ├── module.config.php
    ├── Contracts/
    │   ├── Actions/              # Action interfaces (CreateItemAction.php)
    │   ├── Queries/              # Query contracts (ItemQueryContract.php)
    │   └── Resolvers/
    ├── Data/
    │   ├── Collections/          # Typed DTO collections
    │   ├── DataObjects/        # DTOs (ItemDataObject.php)
    │   ├── Snapshots/
    │   └── ValueObjects/
    ├── Domain/
    │   ├── Actions/              # Concrete action classes (CreateItem.php)
    │   ├── Events/
    │   ├── Listeners/
    │   ├── Queries/              # Concrete query classes
    │   ├── Resolvers/
    │   └── Scopes/               # Query constraints (PublishedItems.php)
    ├── Eloquent/
    │   ├── Models/
    │   └── Observers/
    ├── Enums/
    ├── Http/
    │   ├── Controllers/
    │   ├── Middleware/
    │   └── Requests/
    └── Jobs/
```

### Container Resolution

All dependencies MUST be resolved via the Laravel container — never instantiated directly.

```php
// ✅ Constructor injection (preferred)
class Foo {
    public function __construct(private CreateItemAction $createItem) {}
}

// ✅ Manual resolution when needed
app(RegisterUserWorkflow::class)->handle($data);

// ❌ Never
new RegisterUserWorkflow();
```

### Contract Injection Rule

All cross-boundary dependencies MUST use contracts (interfaces), never concrete classes.

- Controllers → Contracts
- Workflows → Contracts
- Cross-module calls → Contracts only

### Contracts Layer

Contracts define what a module can do publicly. They are the only thing other modules (and workflows) should ever depend on.

**Contracts expose:**
- action capabilities (`CreateItemAction`)
- query capabilities (`ItemQueryContract`)

**Modules MUST NOT expose:**
- workflows
- Eloquent models
- internal query classes
- internal action implementations

### Module Registration

Providers are explicitly listed in `bootstrap/providers.php`. Never use auto-discovery for modules. `php artisan make:module {Name}` scaffolds the structure, adds the PSR-4 entry to `composer.json`, runs `composer dump-autoload`, and registers the provider automatically.

### Creating New Files

Always use `php artisan make:` commands to create new files. Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters. Always pass `--no-interaction`.

All standard `make:*` commands accept a `--module=Name` flag that routes the file to the correct location inside the module automatically.

**Custom make commands (module-aware):**

| Command                 | Output location      | Notes                                                                           |
| ----------------------- | -------------------- | ------------------------------------------------------------------------------- |
| `make:dataobject Name`  | `Data/DataObjects/`  | Generates `{Name}DataObject` — safe to pass with or without `DataObject` suffix |
| `make:valueobject Name` | `Data/ValueObjects/` | Generates `{Name}` class with VO contracts                                      |
| `make:scope Name`       | `Domain/Scopes/`     | Generates Eloquent Scope class                                                  |

**Standard commands with `--module` support:**

```bash
# Inside a module
php artisan make:controller ViewItemController --module=YourModule --no-interaction
php artisan make:model Item --module=YourModule --no-interaction
php artisan make:migration create_items_table --module=YourModule --no-interaction
php artisan make:event ItemCreated --module=YourModule --no-interaction
php artisan make:job ProcessItemScore --module=YourModule --no-interaction
php artisan make:request CreateItemRequest --module=YourModule --no-interaction
php artisan make:dataobject Item --module=YourModule --no-interaction
php artisan make:valueobject ItemCode --module=YourModule --no-interaction
php artisan make:scope PublishedItems --module=YourModule --no-interaction

# App-level (no --module, goes to app/)
php artisan make:class Support/Http/AppResponse --no-interaction
```

When creating a new model, always create its factory and seeder too. Pass `--factory` to `make:model` to generate both and wire the `#[UseFactory]` / `#[UseModel]` attributes automatically, or create them separately and add the attributes manually.

---

## Decision Table

Use this table when you're unsure which pattern to reach for.

### Action vs Workflow

| Condition                                | Use                                                  |
| ---------------------------------------- | ---------------------------------------------------- |
| Use case touches **one module only**     | Action contract — inject directly into controller    |
| Use case touches **two or more modules** | Workflow in `App\Workflows` — inject into controller |

A Workflow that only ever calls one module's contracts is a sign you don't need a Workflow.

### Event (async) vs Sync cross-module call

| Condition                                                                                 | Use                                              |
| ----------------------------------------------------------------------------------------- | ------------------------------------------------ |
| Caller **cannot wait** for the result (side effect, notification, feed update, audit log) | `event(new SomethingHappened($snapshot))`        |
| Caller **needs the result** before it can return (data required to build response)        | Call Query contract → get Snapshot synchronously |

Default: prefer Events. Only go synchronous when the return value is structurally required.

### Action vs Job

| Condition                                                                                 | Use                                       |
| ----------------------------------------------------------------------------------------- | ----------------------------------------- |
| User waits for result / must be synchronous                                               | Action                                    |
| Can be deferred, retried, or queued (push notification, email, score recalc, feed update) | Job dispatched from an Action or Listener |

Jobs are dispatched **from** Actions or Listeners — never from controllers directly.

### Query vs Action

| Condition                      | Use                       |
| ------------------------------ | ------------------------- |
| Reading data, no side effects  | Query contract + Scope(s) |
| Writing data, has side effects | Action contract           |

Queries never mutate. Actions never read-and-return without also writing.

---

## Mandatory Patterns

### 1. Controllers — Final, Invokable, One Per Use Case

Every controller is `final`, invokable (`__invoke`), and handles exactly one use case. No multi-method controllers. Controllers extend `App\Http\Controllers\Controller`.

**Naming convention — use-case style, `Controller` suffix:**

| Scenario            | Example                           |
| ------------------- | --------------------------------- |
| Register user       | `RegisterUserController`          |
| Login               | `LoginController`                 |
| Logout              | `LogoutController`                |
| View profile        | `ViewProfileController`           |
| Update settings     | `UpdateSettingsController`        |
| Delete account      | `DeleteAccountController`         |
| Send password reset | `SendPasswordResetLinkController` |

**Rules:**
- `final` — always
- One `__invoke` method — no multi-method controllers
- Inject contracts only — never concrete classes
- Delegate immediately — no business logic, no `Gate::authorize()`
- Always `app_response()` — never `inertia()`, `Inertia::render()`, or `response()->json()`
- Middleware at route level — not in constructor
- Single-module use case → inject Action/Query contract; cross-module → inject Workflow
- Route syntax: `Route::post('/path', MyController::class)` — invokable, never array syntax

> See the `make-controller` skill for full scaffolding steps and code templates.

### 2. Workflows — Cross-Module Orchestration Only

Workflows coordinate multiple modules. They live exclusively in `App\Workflows` — there is no `Workflows/` folder inside any module, ever. Modules never create, own, or expose workflows.

**Workflows MAY:**
- call module Action Contracts
- call module Query Contracts
- coordinate multiple modules
- manage DB transactions
- convert a module's DataObject to a Snapshot (`->toSnapshot()`) when passing it into another module's Action/Query, or when returning to the caller

A Workflow's own return value is always a Snapshot (or a collection/composite of Snapshots) — never a DataObject. A Workflow only exists because it spans ≥2 modules, so whatever it hands back is by definition crossing a module boundary. See §5/§7 for the DataObject-vs-Snapshot rule this follows.

**Workflows MUST NOT:**
- contain business logic (no domain-rule `if` statements)
- access models directly
- bypass module contracts
- live inside any module

> See the `make-workflow` skill for full scaffolding steps and code templates.

### 3. Actions — Module-Scoped Write Operations

Actions contain all business logic within a module. The contract-bound action is the module's public API entry point for that capability. Internal actions do one thing and are never bound to a contract.

**Naming convention:**
- Interface (in `Contracts/Actions/`): `CreateItemAction` — `Action` suffix
- Concrete (in `Domain/Actions/`): `CreateItem` — no suffix, implements the interface

**Rules:**
- `final` — always
- Receive DataObjects, return DataObjects — never Eloquent models, and never Snapshots. An Action's return value is consumed by a Controller or a Workflow in the *same* call — it hasn't crossed a module boundary yet, so it stays in the module's native DataObject shape. See §5/§7.
- Inject the interface, never the concrete class
- `Gate::authorize()` lives in Actions — not controllers
- One Action per use case — no god methods
- Wrap writes in `DB::transaction()`
- Fire events with `toSnapshot()` — never raw models, and never the DataObject you're about to return. Build the Snapshot from the model separately from the DataObject you return — two conversions, one for the return value, one for the event.
- Bind in `ServiceProvider::register()` — never auto-discovered

```php
// ✅ Action returns a DataObject, fires the event with a Snapshot built alongside it
final class CreateItem implements CreateItemAction
{
    public function handle(CreateItemDataObject $data): ItemDataObject
    {
        return DB::transaction(function () use ($data): ItemDataObject {
            $item = Item::create([...]);

            event(new ItemCreated($item->toSnapshot()));

            return ItemDataObject::fromModel($item);
        });
    }
}

// ❌ Never return the Snapshot from the Action itself
public function handle(CreateItemDataObject $data): ItemSnapshot { ... }
```

> See the `make-action` skill for full scaffolding steps and code templates.

### 4. Query System — Scope-Driven

Queries are composable read systems. Scopes filter rows. Column selection is explicit inline — never SELECT *.

**One Query class per entity — not one per use case.** A Query class distinguishes only by **result shape** (one row / many rows / paginated). It never distinguishes by which column is filtered. Filtering variation belongs entirely to Scope objects, composed by the caller. This is what prevents Query-class explosion — you never write `FindItemByDomain`, `FindItemById`, `FindItemBySlug`, etc. `first()` / `get()` / `paginate()` taking variadic Scopes **is** the generic "Find" — it's a parameter, not a class.

**Naming convention:**
- Contract (in `Contracts/Queries/`): `ItemQueryContract` — `QueryContract` suffix
- Concrete (in `Domain/Queries/`): `ItemQuery` — `Query` suffix
- Scope interface: `{Entity}Scope` (e.g. `ItemScope`)
- Scope class: descriptive phrase (e.g. `PublishedItems`, `WithSlug`)

**Contract shape — fixed, three methods, no named finders:**

```php
interface ItemQueryContract
{
    public function first(ItemScope ...$scopes): ?ItemDataObject;

    public function get(ItemScope ...$scopes): ItemCollection;

    public function paginate(int $perPage, ItemScope ...$scopes): ItemCollection;
}
```

```php
// ✅ Caller composes scopes at the call site
app(ItemQueryContract::class)->first(new WithSlug($slug));
app(ItemQueryContract::class)->first(new WithDomain($domain));
app(ItemQueryContract::class)->get(new PublishedItems(), new WithAuthor($authorId));

// ❌ Never add named finder methods — each one hardcodes a Scope that should've been passed in
public function findBySlug(string $slug): ?ItemDataObject { ... }
public function findByDomain(string $domain): ?ItemDataObject { ... }
```

**Rules:**
- Queries never mutate state — reads only
- Queries never return Eloquent models — always DTOs or Collections
- Every filter uses a Scope class — no raw `where()` inside Query classes
- Every query uses explicit `select([...])` — no SELECT *
- Scope classes are `final`
- Query concrete is `final`
- Build the `Builder` fresh inside each method call — never cache a `Builder` on `$this` across calls. A Query resolved through the container must not carry filter state between calls; scopes are applied per-call from the arguments, not accumulated on the instance.
- No `matching()`-style stateful/fluent builder method on the Query itself — passing scopes as arguments to `first()`/`get()`/`paginate()` replaces it entirely

**Combining Scopes — AND by default, OR lives inside one Scope.**

Every Scope passed to `first()`/`get()`/`paginate()` is AND'd together automatically — the Query loops over them and calls `apply()` on each. For "A and B", just pass two Scopes:

```php
app(UserQueryContract::class)->get(
    new ActiveUsers(),
    new WithRole($role),
);
```

When a condition needs OR logic (e.g. "published OR commented in a date range"), don't invent a combinator — write **one Scope** for that specific condition, with the OR grouped inside its own closure:

```php
final class PublishedOrCommentedBetween implements UserScope
{
    public function __construct(
        private readonly CarbonImmutable $start,
        private readonly CarbonImmutable $end,
    ) {}

    public function apply(Builder $query): void
    {
        $query->where(fn (Builder $q) => $q
            ->whereHas('posts', fn (Builder $p) => $p->whereBetween('published_at', [$this->start, $this->end]))
            ->orWhereHas('comments', fn (Builder $c) => $c->whereBetween('created_at', [$this->start, $this->end])));
    }
}
```

Used exactly like any other Scope, AND'd alongside the rest:

```php
app(UserQueryContract::class)->get(
    new ActiveUsers(),
    new WithRole($role),
    new PublishedOrCommentedBetween($start, $end),
);
```

**Rule: any Scope with an internal `orWhere`/`orWhereHas` must wrap its own conditions in a single `where(fn (Builder $q) => ...)` closure.** Without the wrapper, the OR leaks out of the scope and breaks the AND precedence with sibling scopes (`active AND role OR published...` instead of `active AND role AND (published OR commented)`). The closure is what keeps the OR contained to one AND'd group.

There is no generic `AnyOf`/`AllOf` combinator — that's premature abstraction for a problem most queries don't have. If a genuinely different OR-shaped condition comes up later, it's another single-purpose Scope, not a tree of composable primitives.

> See the `make-query` skill for full scaffolding steps and code templates.

### 5. Data Layer Hierarchy

| Type               | Purpose                                                | Validates?                 |
| ------------------ | ------------------------------------------------------ | -------------------------- |
| `ValueObject`      | Represents a domain concept (StarRating, EmailAddress) | Yes — throws on bad data   |
| `DataObject (in)`  | Carries request data into an Action                    | No — FormRequest does that |
| `DataObject (out)` | Carries result data out of an Action or Query          | No                         |
| `Collection`       | Typed container for DTOs                               | No                         |
| `Snapshot`         | Cross-module data contract                             | No                         |

**DataObject vs Snapshot — the rule is "has it crossed a module boundary yet?"** An Action or Query's native return type is always a DataObject (or a Collection of them) — that's true regardless of who's calling it, Controller or Workflow. A Snapshot only gets created at the exact point data crosses out of its owning module: firing an Event, or a Workflow pulling one module's DataObject to feed into another module's Action/Query (`$dataObject->toSnapshot()`). Never make an Action or Query return a Snapshot directly — that bakes the boundary conversion into the module's internal API instead of doing it at the actual crossing point.

**Never skip a layer.** Don't pass raw arrays into Actions. Don't return Eloquent models from Actions. Don't use Eloquent API Resources — use DTOs consistently throughout.

Typed collections use the `DataCollection` trait:
```php
final class ItemCollection
{
    use DataCollection;

    /** @param list<ItemDataObject> $items */
    public function __construct(private readonly array $items = []) {}

    public static function fromModels(Collection $models): static { ... }
}
```

### 6. AppResponse — Always Use This

All responses — API and Inertia — share a single envelope shape. The correct response type is detected automatically from the request context.

#### Envelope Shape

```json
{
    "success": true,
    "message": "success",
    "code": 200,
    "status": "OK",
    "data": {},
    "errors": {},
    "meta": {}
}
```

| Field     | Type     | Notes                                                                |
| --------- | -------- | -------------------------------------------------------------------- |
| `success` | `bool`   | `true` for 200/201, `false` for all others                           |
| `message` | `string` | Human-readable summary                                               |
| `code`    | `int`    | HTTP status code integer                                             |
| `status`  | `string` | HTTP status text (`"OK"`, `"Unprocessable Entity"`, …)               |
| `data`    | `array`  | Response payload. Array for collections, object for single resources |
| `errors`  | `object` | Field-keyed errors. Always an object, never an array                 |
| `meta`    | `object` | Pagination metadata when paginated. Empty object otherwise           |

`errors` and `meta` serialize as `{}` when empty — never `[]`.

#### Response Types

**API (JSON):** Returned when `$page` is `null`, or `$page` is set but the request does not include the `X-Inertia` header.

**Inertia:** Returned when `$page` is set **and** the request includes `X-Inertia: true`. The envelope fields are the top-level Inertia props.

```typescript
// Typed shared props on the frontend
const props = defineProps<{
    success: boolean
    message: string
    code: number
    status: string
    data: Record<string, unknown> | unknown[]
    errors: Record<string, string[]>
    meta: Record<string, unknown>
}>()
```

#### Usage

```php
// Success with data
return app_response('YourModule/Show', $item->toArray());

// Success with message
return app_response('YourModule/Index', $items, 'Items loaded');

// Paginated data (meta auto-populated)
return app_response('YourModule/Index', Item::paginate(15));

// Explicit HTTP status
return app_response('YourModule/Show', $item->toArray(), 'Created', HttpResponse::CREATED);

// API-only (no page)
return app_response(null, $user->toArray());

// Force JSON (NativePHP background sync, API endpoints)
return app_response('YourModule/Index', $dto->toArray())->forceJson();

// Force Inertia (rare — only when you explicitly need SSR props)
return app_response('YourModule/Index', $dto->toArray())->forceInertia();
```

#### AppResponseFactory

Use when you need `fail()` or `error()` shortcuts. **Note:** `FormRequest` validation failures are handled automatically — Laravel throws `ValidationException`, Inertia intercepts it, and `HandleInertiaRequests` maps it into `errors` props. Only call `fail()` for manual validation.

```php
// Validation failure (422) — field-level errors
return AppResponseFactory::fail('Auth/Register', [
    'email'    => ['Already taken.'],
    'password' => ['Must be at least 8 characters.'],
]);
```

```json
{
    "success": false,
    "code": 422,
    "status": "Unprocessable Entity",
    "errors": { "email": ["Already taken."], "password": ["Must be at least 8 characters."] }
}
```

```php
// General error (authorization, server errors) — keyed under `general`
return AppResponseFactory::error('YourModule/Show', 'You do not have permission.');
```

```json
{
    "success": false,
    "code": 500,
    "status": "Internal Server Error",
    "errors": { "general": ["You do not have permission."] }
}
```

```php
// General error with custom status
return AppResponseFactory::error(null, 'Not authorized', HttpResponse::FORBIDDEN);
```

#### Flash Errors (Inertia Redirects)

When redirecting (e.g. failed login), errors go to session:

```php
return redirect()->back()->with('error', 'Invalid credentials.');
return redirect()->route('dashboard')->with('success', 'Logged in.');
```

`HandleInertiaRequests` merges both session sources into the shared `errors` prop:

| Session source                               | Mapped to        |
| -------------------------------------------- | ---------------- |
| `$errors` bag (FormRequest / `withErrors()`) | `errors.{field}` |
| `session('error')` flash                     | `errors.general` |

```typescript
const page = usePage()
const emailError = page.props.errors?.email?.[0]    // field error
const generalError = page.props.errors?.general?.[0] // flash or AppResponseFactory::error()
const successMessage = page.props.flash?.success     // flash success — separate from errors envelope
```

#### Paginated Responses

Pass a `LengthAwarePaginator` directly — `data` and `meta` are populated automatically.

```php
return app_response('YourModule/Index', Item::paginate(15));
```

```json
{
    "success": true,
    "data": [{ "id": 1 }, { "id": 2 }],
    "meta": {
        "current_page": 1,
        "last_page": 4,
        "per_page": 15,
        "total": 60,
        "from": 1,
        "to": 15
    }
}
```

#### HttpResponse Enum

```php
HttpResponse::OK            // 200
HttpResponse::CREATED       // 201
HttpResponse::BAD_REQUEST   // 400
HttpResponse::UNAUTHORIZED  // 401
HttpResponse::FORBIDDEN     // 403
HttpResponse::NOT_FOUND     // 404
HttpResponse::UNPROCESSABLE // 422
HttpResponse::SERVER_ERROR  // 500
```

**Never call `inertia()`, `Inertia::render()`, or `response()->json()` directly in controllers.**

### 7. Cross-Module Communication

Cross-module data flows via **Events** (async) or **Snapshots** (sync). Never raw models, never a DataObject handed directly to another module, never direct Domain imports.

A module's Action/Query contract always returns its own DataObject natively (§5). When that data needs to reach another module — always mediated by a Workflow, never a direct module-to-module call — the Workflow converts it at the point of crossing: `$dataObject->toSnapshot()`.

| Scenario                      | Use                                                          |
| ----------------------------- | ------------------------------------------------------------- |
| Data must exist before return | Workflow calls Query contract (gets DataObject), converts to Snapshot to pass along or return |
| Recalculate a derived value   | Sync call                                                      |
| Send push notification        | Event                                                          |
| Update a feed or aggregate    | Event                                                          |
| Log audit trail               | Event                                                          |

Event payloads always carry a Snapshot, never a model and never a DataObject: `event(new ItemCreated($item->toSnapshot()))`.

> See the `make-snapshot` skill for full scaffolding steps and code templates.

### 8. Value Objects

- `final readonly`, uses `ValueObjectMaker` trait, implements `App\Contracts\ValueObject`
- Validate in constructor — throw `\InvalidArgumentException` on bad data
- Implement `equals()` and `__toString()`
- Named static factory: `StarRating::from($value)`

### 9. Final Class Policy

**These MUST always be `final`:**

- Controllers
- Actions
- Queries
- Scopes
- DTOs
- Snapshots
- Collections
- ValueObjects
- Events
- Listeners
- Workflows

### 10. Soft Deletes and Hard Deletes

Models with `SoftDeletes`: # your soft-delete models

Hard deletes are **always** done through a dedicated Action — never raw `forceDelete()` in a controller or Filament resource directly. Hard delete Actions must:
1. Handle cascade deletions explicitly
2. Recalculate any affected derived values
3. Clear associated media
4. Write an audit log entry

### 11. Premium Feature Gating

Gate checks in Actions via `Gate::authorize()`. All gates are defined in `BillingServiceProvider`. Route-level gating via the `RequiresPremium` middleware for whole endpoints.

```php
// In an Action
Gate::authorize('feature-name');

// In routes
Route::post('/feature', FeatureController::class)
    ->middleware(['auth', 'premium', 'throttle:10,1']);
```

Feature flags via Pennant — defined in `FeatureServiceProvider`:
- `feature-name`
- `another-feature`

Local override: `PREMIUM_OVERRIDE=true` in `.env` bypasses premium checks in the local environment only.

### 12. Naming Conventions

| Concern         | Pattern                 | Example                |
| --------------- | ----------------------- | ---------------------- |
| Action Contract | `{Action}Action`        | `CreateUserAction`     |
| Action          | `{Action}`              | `CreateUser`           |
| Query Contract  | `{Entity}QueryContract` | `UserQueryContract`    |
| Query           | `{Entity}Query`         | `UserQuery`            |
| Scope interface | `{Entity}Scope`         | `ItemScope`            |
| Scope class     | descriptive             | `PublishedItems`       |
| Workflow        | `{Process}Workflow`     | `RegisterUserWorkflow` |
| Controller      | `{UseCase}Controller`   | `LoginController`      |
| DataObject      | `{Concept}DataObject`   | `ItemDataObject`       |
| Snapshot        | `{Concept}Snapshot`     | `ItemSnapshot`         |
| Collection      | `{Concept}Collection`   | `ItemCollection`       |
| ValueObject     | domain concept          | `EmailAddress`         |
| Event           | past tense              | `ItemCreated`          |

---

## Forbidden Patterns

These are never acceptable — push back if asked to do any of these:

```php
// ❌ Never import a model from another module
use App\OtherModule\Eloquent\Models\Item; // inside YourModule — use a Snapshot instead

// ❌ Never return Eloquent models from Actions
public function handle(): Item { ... }

// ❌ Never put business logic in controllers
public function __invoke(Request $request) {
    $item = Item::create([...]);
    event(new ItemCreated($item));
}

// ❌ Never put business logic in Workflows
// Workflows coordinate — business rules live in Actions

// ❌ Never call inertia() or Inertia::render() directly
return inertia('YourModule/Show', [...]);
return Inertia::render('YourModule/Show', [...]);

// ❌ Never call response()->json() in controllers
return response()->json($item->toArray());

// ❌ Never use forceDelete() directly
$user->forceDelete();

// ❌ Never skip DataObjects — don't pass raw arrays into Actions
CreateItemAction::handle($request->all(), $user);

// ❌ Never use Eloquent API Resources — use DTOs
return new ItemResource($item);

// ❌ Never create verification/debugging scripts — write tests instead

// ❌ Never instantiate Actions, Queries, or Workflows with new
new RegisterUserWorkflow(); // use app(RegisterUserWorkflow::class)

// ❌ Never bypass scopes in queries
Item::where('slug', $slug)->first(); // in a Query class — use a Scope

// ❌ Never hardcode routes on the frontend — use Wayfinder

// ❌ Never use relative imports on the frontend — use the @ alias
import Foo from '../components/Foo.vue' // use @/components/Foo.vue

// ❌ Never use npm or yarn — pnpm only

// ❌ Never use newFactory() or protected $model in factories — use #[UseFactory] / #[UseModel] attributes
protected static function newFactory(): ItemFactory { ... }
protected $model = Item::class;

// ❌ Never use SCSS — scoped CSS only

// ❌ Never create a Workflow inside a module
// No Workflows/ folder exists in any module — ever
// Workflows live only in App\Workflows
```

---

## PHP Conventions

Follow PHP 8.4 conventions throughout. These are non-negotiable.

- Prefer traits over abstract base classes — share behavior via traits, not inheritance
- Constructor property promotion — always (`public readonly Foo $foo` in constructor, never a separate property declaration)
- No empty zero-parameter `__construct()` unless the constructor is private
- Explicit return types and type hints on all methods — always
- PHPDoc only for array shapes (`@param array{name: string}`) and generics — no prose docblocks
- Enum cases in UPPER_CASE (`case LIGHT = 'light'`)
- Always curly braces on control structures — no one-liner `if`
- Descriptive boolean names: `$isRegisteredForPremium` not `$premium`

### Model / Factory Wiring — Attribute Pattern

Use Laravel 13 attributes instead of `newFactory()` / `protected $model`.

**Model** — `#[UseFactory]` on the class, no `newFactory()` method:

```php
use App\YourModule\Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(ItemFactory::class)]
final class Item extends Model
{
    use HasFactory;
}
```

**Factory** — `#[UseModel]` on the class, no `protected $model` property:

```php
use App\YourModule\Eloquent\Models\Item;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

#[UseModel(Item::class)]
final class ItemFactory extends Factory
{
    public function definition(): array { ... }
}
```

The `make:model --factory` command generates both files wired correctly. When creating separately, add the attributes manually — never use `newFactory()` or `protected $model`.

### Pint — Run After Every PHP Change

Always run Pint after modifying any PHP files:

```bash
cd backend && vendor/bin/pint --dirty --format agent
```

---

## Frontend Architecture

### Structure

```
frontend/
├── app.ts
├── app.css
├── components/           # Global primitives (AppPremiumGate, AppToast, …)
├── composables/          # Cross-cutting composables (useAuth, …)
├── domains/              # Reusable UI tied to backend concepts
│   ├── {YourModule}/     #   module-specific widgets and cards
│   └── …
├── pages/                # Full page compositions
│   ├── layouts/          #   Page templates
│   ├── Auth/
│   ├── {YourModule}/
│   └── …
├── types/                # Shared TS declarations + Wayfinder-generated types (never edit manually)
```

**Dependency rule:**
```
pages → domains → components
```

- `domains` MUST NOT import from `pages`
- `components` MUST NOT depend on `domains` or `pages`

**Import rules:**
- Always use the `@` alias — `@` maps to `frontend/`
- Never use relative paths (`../`) — oxlint enforces this

```typescript
// ✅ Correct
import AppLayout from '@/components/AppLayout.vue'
import { useAuth } from '@/composables/useAuth'
import ItemWidget from '@/domains/YourModule/ItemWidget.vue'
import { route } from '@/types'

// ❌ Wrong
import AppLayout from '../components/AppLayout.vue'
```

### Vue 3 + Inertia 3 Patterns

```typescript
// Always use router from @inertiajs/vue3 (Inertia 3 — not legacy Inertia object)
import { router } from '@inertiajs/vue3'

// router.cancel() has been removed in v3 — use router.cancelAll()
router.cancelAll()

// Always type props explicitly
const props = defineProps<{ item: ItemData; related?: RelatedData }>()

// Shared props via usePage — always typed
const page = usePage<SharedProps>()
```

### Inertia 3 — Key Changes

- `Inertia::lazy()` / `LazyProp` removed — use `Inertia::optional()` instead
- Axios removed — use the built-in XHR client (`useHttp` hook) or install Axios separately
- `router.cancel()` replaced by `router.cancelAll()`
- `future` config namespace removed — all v2 future options are now always enabled
- New: `useHttp` hook for standalone HTTP requests outside of page visits
- New: optimistic updates with automatic rollback
- New: `useLayoutProps` hook
- Deferred props: always add a pulsing skeleton as the empty/loading state

### Wayfinder — No Hardcoded Routes

Backend is the source of truth. Frontend uses Wayfinder-generated routes. No string routes allowed.

```typescript
// ✅ Correct
import { route } from '@/types'
router.post(route('items.create', { slug: item.slug }), data)

// ❌ Wrong
router.post('/items/create/' + item.slug, data)
```

### Premium Gating on the Frontend

Always use the `AppPremiumGate` component to wrap premium features — never scatter raw boolean checks through templates.

```vue
<AppPremiumGate>
  <PremiumFeatureComponent v-model="value" />
</AppPremiumGate>
```

Premium status comes from `page.props.auth.user.is_premium` — set server-side in `HandleInertiaRequests::share()` on every request.

### WebAwesome

Use WebAwesome components throughout (`wa-button`, `wa-icon`, `wa-card`, etc.). Do not introduce other UI libraries. Check for existing components before writing new ones. Custom components live in `frontend/components/` or the relevant domain folder.

### Frontend Standards

- Vue 3 + TypeScript only
- pnpm only (never npm or yarn)
- WebAwesome as the UI system
- Scoped CSS only — no SCSS
- Wayfinder required — no hardcoded route strings

### Frontend Build

If a frontend change isn't reflected in the UI, run `task dev:frontend` or `task build`. Don't assume a code bug until the build has been refreshed.

---

## Testing

### Backend (Pest 4)

Create tests with:
```bash
cd backend && php artisan make:test --pest {Name} --no-interaction          # feature test
cd backend && php artisan make:test --pest --unit {Name} --no-interaction   # unit test
```

Run tests with:
```bash
php artisan test --compact                        # all tests
php artisan test --compact --filter=testName      # filtered
```

Tests live inside each module at `modules/{Name}/tests/`. The root `tests/Pest.php` discovers them via glob.

```php
// Unit tests — pure PHP, no DB, no HTTP
it('rejects invalid values', function (mixed $value) {
    expect(fn () => MyValueObject::from($value))
        ->toThrow(\InvalidArgumentException::class);
})->with([/* invalid cases */]);

// Feature tests — full HTTP, uses RefreshDatabase
it('can create an item', function () {
    $user = User::factory()->create();
    $item = Item::factory()->published()->create();

    $response = $this->actingAs($user)
        ->post("/items/{$item->slug}", [...]);

    $response->assertRedirect();
    $this->assertDatabaseHas('items', ['user_id' => $user->id]);
});
```

**Rules:**
- Always use model factories in tests — check for existing factory states before manually setting attributes
- Do not delete tests without approval
- Do not create verification/debugging scripts — write a test instead

### PHPStan Enforcement

PHPStan MUST enforce:
- Module isolation (no cross-module model imports)
- Contract-only cross-boundary usage
- No model leakage between modules
- No workflow exposure from modules
- `final` class rules

### Pest Architecture Tests

Pest MUST enforce:
- Controller boundaries (final, invokable, no business logic)
- Workflow isolation (live only in `App\Workflows`)
- Module boundaries (no internal leakage)

### Frontend (Vitest)

Tests live at `pages/{Module}/__tests__/` and `domains/{Module}/__tests__/`. Component tests use `@vue/test-utils`.

```bash
task test:frontend        # run once
task test:frontend:watch  # watch mode
```

### ESLint Enforcement

ESLint MUST enforce:
- Frontend dependency direction (pages → domains → components)
- No hardcoded route strings
- No direct `inertia()` / `Inertia.render()` calls

---

## Key Commands

```bash
# Development
task dev                        # Vite + Horizon + Reverb + Stripe CLI
task dev:frontend               # Vite only
task dev:queue                  # Horizon only
task dev:reverb                 # Reverb only
task dev:stripe                 # Stripe webhook forwarding

# Database
task migrate                    # run pending migrations
task migrate:fresh              # drop all + remigrate + seed (⚠️ destructive)

# Testing
task test                            # all backend tests (parallel)
task test:unit                       # unit tests only
task test:feature                    # feature tests only
task test:module NAME=YourModule     # single module
task test:frontend                   # Vitest
task test:all                        # backend + frontend

# Scaffolding
task module:make NAME=YourModule     # scaffold a new module

# Code quality
cd backend && vendor/bin/pint --dirty --format agent   # format changed PHP files

# Artisan helpers
php artisan route:list --except-vendor              # inspect routes
php artisan config:show app.name                    # read config values
```

---

## Rate Limiting

| Endpoint             | Limit         |
| -------------------- | ------------- |
| `POST /api/endpoint` | 10 per minute |
| `POST /api/other`    | 30 per minute |

---

## Module Responsibilities

Define module responsibilities in a table here once your modules are established. See `backend/module-structure.yml` for the canonical module structure.

---

## NativePHP

NativePHP is a deployment target only — it must not affect architecture rules or bleed into module internals.

- No coupling between NativePHP concerns and workflows
- No coupling between NativePHP concerns and module internals
- Use `->forceJson()` on `app_response()` for NativePHP background sync endpoints

