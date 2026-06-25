---
name: make-query
description: "Use this skill when creating a Query or Scope. Triggers on: 'create a query', 'make a query', 'add scope', 'new scope', 'read-only query', 'fetch data', 'list query'. Covers: QueryContract interface, Query concrete class, Scope classes, and binding in the ServiceProvider. Queries never mutate state."
license: MIT
metadata:
  author: your-app
---

# Make Query

Queries are composable read systems. Scopes filter rows. Column selection is explicit inline — never SELECT *.

## Naming

| Part | Pattern | Example |
|------|---------|---------|
| Contract | `{Entity}QueryContract` | `ItemQueryContract` |
| Concrete | `{Entity}Query` | `ItemQuery` |
| Scope interface | `{Entity}Scope` | `ItemScope` |
| Scope class | descriptive phrase | `PublishedItems`, `WithSlug`, `ForUser` |

## Step 1 — Create the contract

```bash
cd backend && php artisan make:interface Contracts/Queries/{Entity}QueryContract --module={Name} --no-interaction
```

```php
<?php declare(strict_types=1);

namespace App\{Name}\Contracts\Queries;

use App\{Name}\Data\Collections\{Entity}Collection;
use App\{Name}\Data\DomainObjects\{Entity}DataObject;
use App\{Name}\Domain\Scopes\{Entity}Scope;

interface {Entity}QueryContract
{
    public function matching({Entity}Scope ...$scopes): static;

    public function first(): ?{Entity}DataObject;
    public function get(): {Entity}Collection;
    public function paginate(int $perPage = 15): {Entity}Collection;
}
```

## Step 2 — Create the Scope interface

```bash
cd backend && php artisan make:interface Domain/Scopes/{Entity}Scope --module={Name} --no-interaction
```

```php
<?php declare(strict_types=1);

namespace App\{Name}\Domain\Scopes;

use Illuminate\Database\Eloquent\Builder;

interface {Entity}Scope
{
    public function apply(Builder $query): void;
}
```

## Step 3 — Create Scope classes

```bash
cd backend && php artisan make:scope {ScopeName} --module={Name} --no-interaction
```

```php
<?php declare(strict_types=1);

namespace App\{Name}\Domain\Scopes;

use Illuminate\Database\Eloquent\Builder;

final class {ScopeName} implements {Entity}Scope
{
    public function __construct(private readonly string $value) {}

    public function apply(Builder $query): void
    {
        $query->where('column', $this->value);
    }
}
```

## Step 4 — Create the concrete Query

```bash
cd backend && php artisan make:class Domain/Queries/{Entity}Query --module={Name} --no-interaction
```

```php
<?php declare(strict_types=1);

namespace App\{Name}\Domain\Queries;

use App\{Name}\Contracts\Queries\{Entity}QueryContract;
use App\{Name}\Data\Collections\{Entity}Collection;
use App\{Name}\Data\DomainObjects\{Entity}DataObject;
use App\{Name}\Domain\Scopes\{Entity}Scope;
use App\{Name}\Eloquent\Models\{Entity};

final class {Entity}Query implements {Entity}QueryContract
{
    private \Illuminate\Database\Eloquent\Builder $builder;

    public function __construct()
    {
        $this->builder = {Entity}::query();
    }

    public function matching({Entity}Scope ...$scopes): static
    {
        foreach ($scopes as $scope) {
            $scope->apply($this->builder);
        }

        return $this;
    }

    public function first(): ?{Entity}DataObject
    {
        $model = $this->builder->first();

        return $model ? {Entity}DataObject::fromModel($model) : null;
    }

    public function get(): {Entity}Collection
    {
        return {Entity}Collection::fromModels($this->builder->get());
    }

    public function paginate(int $perPage = 15): {Entity}Collection
    {
        return {Entity}Collection::fromPaginator($this->builder->paginate($perPage));
    }
}
```

## Step 5 — Bind in ServiceProvider

```php
$this->app->bind(
    \App\{Name}\Contracts\Queries\{Entity}QueryContract::class,
    \App\{Name}\Domain\Queries\{Entity}Query::class,
);
```

## Step 6 — Run Pint

```bash
cd backend && vendor/bin/pint --dirty --format agent
```

## Rules

- Scopes filter rows — always use a Scope class, even for simple filters
- Column selection is explicit inline `select([...])` in the Query class — never SELECT *
- Queries never mutate state — reads only
- Queries never return Eloquent models — always DTOs or Collections
- Scope classes are `final`, implement the `{Entity}Scope` interface
- The Query concrete is `final`
- Never bypass scopes with raw `where()` calls inside the Query class itself
