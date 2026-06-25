---
name: make-module
description: "Use this skill when scaffolding a new module. Triggers on: 'create a module', 'make a module', 'scaffold module', 'new module', 'add module'. Covers the full setup: artisan scaffold, composer registration, provider wiring, and confirming the module directory structure is correct."
license: MIT
metadata:
  author: your-app
---

# Make Module

Scaffold a complete new module. Follow these steps exactly and in order.

## Step 1 — Scaffold

```bash
cd backend && php artisan make:module {Name} --no-interaction
```

This creates the full directory structure under `modules/{Name}/`, adds the PSR-4 entry to `composer.json`, runs `composer dump-autoload`, and registers the provider in `bootstrap/providers.php`.

## Step 2 — Verify provider registration

Confirm `bootstrap/providers.php` contains:
```php
App\{Name}\{Name}ServiceProvider::class,
```

## Step 3 — Verify composer.json

Confirm `composer.json` `autoload.psr-4` contains:
```json
"App\\{Name}\\": "modules/{Name}/src/"
```

## Step 4 — Verify directory structure

The scaffold must produce:
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
    │   ├── Actions/
    │   ├── Queries/
    │   └── Resolvers/
    ├── Data/
    │   ├── Collections/
    │   ├── DomainObjects/
    │   ├── Snapshots/
    │   └── ValueObjects/
    ├── Domain/
    │   ├── Actions/
    │   ├── Events/
    │   ├── Listeners/
    │   ├── Queries/
    │   ├── Resolvers/
    │   └── Scopes/
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

## Step 5 — Register routes

In `{Name}ServiceProvider::boot()`, confirm routes are loaded:
```php
$this->loadRoutesFrom(__DIR__ . '/module.routes.php');
```

## Step 6 — Run architecture tests

```bash
cd backend && php artisan test --testsuite=Architecture --compact
```

All tests should pass (vacuously for empty namespaces).

## Rules

- Module name: PascalCase (e.g. `Catalog`, `Billing`, `Notification`)
- Namespace: `App\{Name}\` — flat, never `App\Modules\{Name}\`
- No `Workflows/` directory inside a module — ever
- All files inside the module must use `declare(strict_types=1)`
