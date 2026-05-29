---
name: make-snapshot
description: "Use this skill when creating a Snapshot for cross-module communication. Triggers on: 'create a snapshot', 'make a snapshot', 'cross-module data', 'share data between modules', 'event payload', 'snapshot for event'. Snapshots are read-only cross-module facts carried inside Events. Never pass Eloquent models or DataObjects across module boundaries."
license: MIT
metadata:
  author: your-app
---

# Make Snapshot

Snapshots are the only way structured data crosses module boundaries. They are final, readonly, and carry no behavior.

## When to use

| Scenario | Pattern |
|----------|---------|
| Module fires an event that another module handles | Attach a Snapshot to the Event |
| Workflow needs data from Module A to pass to Module B | Module A returns a Snapshot from its Query |
| Module B listener needs to act on Module A's data | Read the Snapshot from the Event |

Never pass DataObjects, Eloquent models, or raw arrays across module boundaries.

## Naming

`{Concept}Snapshot` — named after the domain concept, not the module:
- `ItemSnapshot` (not `ItemModuleSnapshot`)
- `UserSnapshot`
- `OrderSnapshot`

## Step 1 — Create the Snapshot

```bash
cd backend && php artisan make:class Data/Snapshots/{Concept}Snapshot --module={Name} --no-interaction
```

## Step 2 — Snapshot content

```php
<?php declare(strict_types=1);

namespace App\{Name}\Data\Snapshots;

use App\Support\Snapshots\Snapshot;
use App\{Name}\Eloquent\Models\{Model};

final readonly class {Concept}Snapshot extends Snapshot
{
    public function __construct(
        public int $id,
        public string $name,
        // ... only the fields other modules need
    ) {}

    public static function fromModel({Model} $model): static
    {
        return new static(
            id: $model->id,
            name: $model->name,
        );
    }
}
```

## Step 3 — Add toSnapshot() to the DataObject

In `{Name}\Data\DomainObjects\{Concept}DataObject`:
```php
public function toSnapshot(): {Concept}Snapshot
{
    return new {Concept}Snapshot(
        id: $this->id,
        name: $this->name,
    );
}
```

## Step 4 — Use inside Events

```php
// In the Event class (stays inside the originating module)
final class {Concept}Created
{
    public function __construct(
        public readonly {Concept}Snapshot $snapshot,
    ) {}
}

// Fired from an Action:
event(new {Concept}Created($result->toSnapshot()));
```

## Step 5 — Consume in a Listener (another module)

```php
// App\{OtherModule}\Domain\Listeners\On{Concept}Created
final class On{Concept}Created
{
    public function handle({Concept}Created $event): void
    {
        // Access: $event->snapshot->id, $event->snapshot->name
        // Never import App\{Name}\Data\DomainObjects or Models from here
    }
}
```

## Step 6 — Run Pint

```bash
cd backend && vendor/bin/pint --dirty --format agent
```

## Rules

- Snapshots are `final readonly` — always
- Extend `App\Support\Snapshots\Snapshot`
- Contain only scalar fields + other Snapshots — no Eloquent models, no DTOs
- Include only the fields other modules actually need — keep them narrow
- A module's Snapshot lives in its own `Data/Snapshots/` directory
- Other modules may import a Snapshot — that is the intended cross-module contract
- Other modules must NEVER import DataObjects, Models, or Domain classes from the originating module
