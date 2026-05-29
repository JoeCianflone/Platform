# Agent Guidelines

Quick reference for AI agents. See CLAUDE.md for full architecture documentation.

---

## Core Rules

1. **Modules own their internals** — never import models from other modules
2. **Cross-module communication ONLY via** — Contracts, Snapshots, or Events
3. **DTOs are immutable** — never return Eloquent models from Actions
4. **Queries never mutate state** — use Scopes, not micro-use-cases
5. **Controllers are thin** — final, invokable, single-purpose only
6. **Workflows orchestrate** — live only in `App\Workflows`, modules never expose them
7. **AppResponse always** — never use `inertia()`, `Inertia::render()`, or `response()->json()`
8. **Container resolution only** — never `new Action()` / `new Workflow()`

---

## Namespaces

| Location | Namespace |
|----------|-----------|
| `backend/app/Workflows/` | `App\Workflows` |
| `backend/app/Support/` | `App\Support` |
| `backend/modules/{Name}/src/` | `App\{Name}` |

PSR-4: `App\{Name}\` → `modules/{Name}/src/`

---

## Naming Conventions

| Type | Pattern | Example |
|------|---------|---------|
| Action Contract | `{Action}Action` | `CreateItemAction` |
| Action | `{Action}` | `CreateItem` |
| Query Contract | `{Entity}QueryContract` | `ItemQueryContract` |
| Query | `{Entity}Query` | `ItemQuery` |
| Scope interface | `{Entity}Scope` | `ItemScope` |
| Scope class | descriptive | `PublishedItems` |
| Projection | `{Purpose}Projection` | `FeedProjection` |
| Workflow | `{Process}Workflow` | `RegisterUserWorkflow` |
| Controller | `{UseCase}Controller` | `LoginController` |
| DataObject | `{Concept}DataObject` | `ItemDataObject` |
| Snapshot | `{Concept}Snapshot` | `ItemSnapshot` |
| Collection | `{Concept}Collection` | `ItemCollection` |
| ValueObject | domain concept | `EmailAddress` |
| Event | past tense | `ItemCreated` |

---

## Response System

All responses use `app_response()` — automatically detects API vs Inertia.

```php
return app_response('YourModule/Show', $item->toArray());
return app_response('YourModule/Index', $items, 'Loaded');
return app_response('YourModule/Index', Item::paginate(15));
return app_response('YourModule/Index', $data)->forceJson();
```

For errors:
```php
return AppResponseFactory::fail('Auth/Register', ['email' => ['Taken.']]);
return AppResponseFactory::error('YourModule/Show', 'Not authorized');
```

---

## Always Use

- **Laravel container** — `app(Class::class)` or constructor injection, never `new Class()`
- **pnpm** — never npm or yarn
- **Wayfinder** — never hardcoded routes
- **WebAwesome** — `wa-button`, `wa-icon`, `wa-card`, etc.
- **Pint** — `cd backend && vendor/bin/pint --dirty --format agent` after every PHP change
- **Final classes** — Controllers, Actions, Queries, Scopes, Projections, DTOs, Snapshots, Events, Workflows

---

## Forbidden Patterns

- ❌ Cross-module model imports
- ❌ Eloquent models returned from Actions
- ❌ Business logic in Controllers or Workflows
- ❌ `inertia()` or `response()->json()` in controllers
- ❌ Hardcoded frontend routes — use Wayfinder
- ❌ Relative imports on the frontend — use `@` alias (`@/components/Foo.vue` not `../components/Foo.vue`)
- ❌ npm/yarn (use pnpm)
- ❌ SCSS (use scoped CSS)
- ❌ `forceDelete()` directly — use dedicated Actions
- ❌ `new Action()` / `new Workflow()` — use container
- ❌ Bypassing Scopes in queries
- ❌ Workflows inside modules — no `Workflows/` folder in any module, ever

---

## Key Commands

```bash
task dev              # Vite + Horizon + Reverb + Stripe
task migrate          # run pending migrations
task test             # run all tests
cd backend && vendor/bin/pint --dirty --format agent  # format PHP
php artisan make:module {Name} --no-interaction       # scaffold module

# All make:* commands accept --module=Name to route into the module
php artisan make:controller ViewItemController --module=YourModule --no-interaction
php artisan make:dataobject Item --module=YourModule --no-interaction        # → Data/DomainObjects/ItemDataObject.php
php artisan make:valueobject ItemCode --module=YourModule --no-interaction   # → Data/ValueObjects/ItemCode.php
php artisan make:scope PublishedItems --module=YourModule --no-interaction   # → Domain/Scopes/PublishedItems.php
php artisan make:event ItemCreated --module=YourModule --no-interaction      # → Domain/Events/ItemCreated.php
php artisan make:model Item --module=YourModule --no-interaction             # → Eloquent/Models/Item.php
php artisan make:migration create_items_table --module=YourModule --no-interaction
```
