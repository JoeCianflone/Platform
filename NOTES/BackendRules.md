## Stack

* PHP 8.4 / Laravel 13 / Inertia Laravel v3
* Pest v4 / PHPUnit v12
* Laravel Pint v1
* Laravel Pennant (feature flags)
* Laravel Cashier + Stripe

## Directory Layout

```
backend/
├── app/
│   ├── Workflows/        # Cross-module orchestration ONLY
│   ├── Support/          # Shared infrastructure (Actions base, DTOs base, etc.)
│   └── Providers/
└── modules/
    └── {Name}/
        ├── database/
        ├── tests/
        └── src/
            ├── Contracts/Actions/
            ├── Contracts/Queries/
            ├── Data/DataObjects/
            ├── Data/Snapshots/
            ├── Data/ValueObjects/
            ├── Domain/Actions/
            ├── Domain/Events/
            ├── Domain/Scopes/
            ├── Eloquent/Models/
            ├── Http/Controllers/
            ├── Http/Requests/
            └── Jobs/
```

PSR-4: `App\{ModuleName}\` → `modules/{ModuleName}/src/`

## The Golden Rule

Modules never import models from other modules. Cross-module communication via Events or Snapshots only. If you're importing an Eloquent model from another module — stop, use a Snapshot instead.

## Request Flow

```
Controller (final, invokable)
  → FormRequest → DTO
  → Workflow (cross-module) OR Action Contract (single-module)
  → Internal Actions / Queries
  → Persistence
  → DTO returned → Snapshot at boundary
  → Controller Response / Event
```

## Controllers

* Always `final`
* Always invokable (`__invoke` only — no multi-method controllers)
* One controller per use case
* Inject contracts only — never concrete classes
* No business logic — delegate immediately
* No `Gate::authorize()` — that lives in Actions
* Always `app_response()` — never `inertia()`, `Inertia::render()`, or `response()->json()`
* Middleware at route level — never in constructor
* Route syntax: `Route::post('/path', MyController::class)`

## Workflows

* Live ONLY in `App\Workflows` — never inside any module
* Coordinate multiple modules via their contracts
* No business logic — only coordination
* May manage DB transactions and DTO mapping
* Never access models directly
* Never bypass module contracts

## Actions

* Always `final`
* Receive DTOs, return DTOs — never Eloquent models
* `Gate::authorize()` lives here
* One action per use case
* Wrap writes in `DB::transaction()`
* Fire events with `toSnapshot()` — never raw models
* Interface in `Contracts/Actions/`, concrete in `Domain/Actions/`
* Naming: interface = `CreateItemAction`, concrete = `CreateItem` (no suffix)
* Bind interface → concrete in `ServiceProvider::register()`

## Queries

* Always `final`
* Never mutate state — reads only
* Never return Eloquent models — always DTOs or Collections
* Every filter uses a Scope class — no raw `where()` inside Query classes
* Every query uses explicit `select([...])` — no SELECT \*
* Interface in `Contracts/Queries/`, concrete in `Domain/Queries/`
* Naming: interface = `ItemQueryContract`, concrete = `ItemQuery`

## Scopes

* Always `final`
* One responsibility: filter rows
* Named descriptively: `PublishedItems`, `WithSlug`, not `ItemScope`
## Data Layer

| Type             | Purpose                           | Validates?               |
| ---------------- | --------------------------------- | ------------------------ |
| ValueObject      | Represents a domain concept       | Yes — throws on bad data |
| DataObject (in)  | Carries request data into Action  | No — FormRequest does it |
| DataObject (out) | Carries result data out of Action | No                       |
| Collection       | Typed DTO container               | No                       |
| Snapshot         | Cross-module read-only fact       | No                       |

* Never skip a layer
* Never pass raw arrays into Actions
* Never return Eloquent models from Actions
* Never use Eloquent API Resources — use DTOs throughout

## Value Objects

* `final readonly`, extends `ValueObject`, implements `App\Contracts\ValueObject`
* Validate in constructor — throw `\InvalidArgumentException` on bad data
* Implement `equals()` and `__toString()`
* Named static factory: `StarRating::from($value)`

## Snapshots

* Read-only cross-module data contracts
* Fired in events: `event(new ItemCreated($item->toSnapshot()))`
* Never pass raw models or DataObjects across module boundaries

## Cross-Module Communication

| Scenario                             | Use                                       |
| ------------------------------------ | ----------------------------------------- |
| Caller needs result before returning | Query contract → Snapshot (sync)          |
| Side effect, notification, audit log | `event(new SomethingHappened($snapshot))` |

Default: prefer Events. Only go sync when the return value is structurally required.

## Action vs Workflow Decision

| Condition                            | Use                                                  |
| ------------------------------------ | ---------------------------------------------------- |
| Use case touches one module          | Action contract → inject into controller             |
| Use case touches two or more modules | Workflow in `App\Workflows` → inject into controller |

## Action vs Job Decision

| Condition                      | Use                                    |
| ------------------------------ | -------------------------------------- |
| User waits for result          | Action                                 |
| Can be deferred/retried/queued | Job dispatched from Action or Listener |

Jobs dispatched FROM Actions or Listeners — never from controllers directly.

## AppResponse

* Always `app_response()` — never bare `inertia()`, `Inertia::render()`, `response()->json()`
* Envelope: `{ success, message, code, status, data, errors, meta }`
* `errors` and `meta` always serialize as `{}` when empty — never `[]`
* Pass `LengthAwarePaginator` directly — `data` and `meta` auto-populated
* Use `AppResponseFactory::fail()` for manual validation errors (422)
* Use `AppResponseFactory::error()` for general/authorization errors
* Use `->forceJson()` for NativePHP background sync endpoints

## Container Resolution

* All dependencies via Laravel container — never `new WorkflowOrAction()`
* Constructor injection preferred
* Manual: `app(RegisterUserWorkflow::class)->handle($data)`
* Cross-boundary dependencies MUST use contracts (interfaces), never concrete classes

## PHP 8.4 Conventions

* Constructor property promotion — always
* No empty zero-parameter `__construct()` unless private
* Explicit return types and type hints on all methods
* PHPDoc only for array shapes and generics — no prose docblocks
* Enum keys in TitleCase (`case Light = 'light'`)
* Always curly braces on control structures
* Descriptive booleans: `$isRegisteredForPremium` not `$premium`

## Final Class Policy

These MUST always be `final`: Controllers, Actions, Queries, Scopes, DTOs, Snapshots, Collections, ValueObjects, Events, Listeners, Workflows.

## Creating New Files

* Always use `php artisan make:` commands — never create manually
* All standard `make:*` commands accept `--module=Name`
* Custom commands: `make:dataobject`, `make:valueobject`, `make:scope`
* Always pass `--no-interaction`
* When creating a model, always create its factory and seeder too

## After Every PHP Change

```bash
cd backend && vendor/bin/pint --dirty --format agent
```

## Testing

* Tests live in `modules/{Name}/tests/`
* Feature tests: full HTTP, use `RefreshDatabase`
* Unit tests: pure PHP, no DB, no HTTP
* Always use model factories — check for existing states before setting attributes manually
* Never delete tests without approval
* Never create verification/debugging scripts — write a test instead

## Forbidden Patterns

* Import model from another module → use Snapshot instead
* Return Eloquent model from Action → return DTO
* Business logic in controllers → delegate to Action/Workflow
* Business logic in Workflows → belongs in Actions
* Call `inertia()`, `Inertia::render()`, `response()->json()` directly
* `$model->forceDelete()` directly → use a dedicated Action
* Pass raw arrays into Actions → use DTOs
* Use Eloquent API Resources → use DTOs
* Instantiate Actions/Queries/Workflows with `new` → use container
* Raw `where()` inside Query classes → use Scopes
* Hardcode routes on frontend → use Wayfinder
* Create a Workflows/ folder inside any module

## Naming Conventions

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

## Module Registration

* Providers explicitly listed in `bootstrap/providers.php`
* Never use auto-discovery for modules
* `php artisan make:module {Name}` scaffolds everything and registers automatically
