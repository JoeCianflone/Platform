# YourApp

A modular Laravel 13 + Inertia 3 + Vue 3 + TypeScript application starter.

---

## Tech Stack

| Layer           | Choice                                      |
| --------------- | ------------------------------------------- |
| Backend         | Laravel 13                                  |
| Frontend        | Inertia 3 + Vue 3 + TypeScript + WebAwesome |
| Mobile          | NativePHP (optional)                        |
| Database        | MySQL 8 + Redis                             |
| Search          | Meilisearch                                 |
| Media           | Spatie Media Library + S3/R2                |
| Queues          | Laravel Horizon                             |
| Websockets      | Laravel Reverb                              |
| Package Manager | pnpm                                        |
| Task Runner     | Task (task.dev)                             |
| Payments        | Laravel Cashier + Stripe                    |

---

## Prerequisites

Install these before anything else.

| Tool                                         | Version | Install                          |
| -------------------------------------------- | ------- | -------------------------------- |
| [Laravel Herd Pro](https://herd.laravel.com) | Latest  | Download from herd.laravel.com   |
| [pnpm](https://pnpm.io)                      | 9+      | `brew install pnpm`              |
| [Task](https://taskfile.dev)                 | 3+      | `brew install go-task`           |
| [TypeScript](https://www.typescriptlang.org) | 5+      | Installed automatically via pnpm |
| [Vite](https://vitejs.dev)                   | 6+      | Installed automatically via pnpm |

**Herd Pro is required** — it bundles MySQL 8, Redis, and Mailpit. The free tier does not include these services.

Once Herd Pro is installed and running, verify your services are active in the Herd menubar app under **Services**. MySQL and Redis should both show as running before proceeding.

### Meilisearch

Meilisearch is not bundled with Herd Pro and must be installed separately:

```bash
brew install meilisearch
brew services start meilisearch
```

Verify it's running at [http://localhost:7700](http://localhost:7700).

---

## Getting Started

### 1. Clone the repo

```bash
git clone git@github.com:your-org/your-app.git
cd your-app
```

### 2. Create the database

Open **Herd Pro → Database** (or TablePlus which Herd bundles) and create a database named `your_app`.

### 3. Run setup

```bash
task setup
```

This runs in order:

```bash
task setup:backend    # composer install, .env copy, key:generate, migrate, seed, storage:link
task setup:frontend   # pnpm install, initial build
task setup:herd       # herd link + herd secure
```

### 4. Manual steps (if you prefer or setup fails)

```bash
# Backend
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

# Frontend
cd frontend
pnpm install
pnpm build

# Register with Herd (or add via Herd UI → Sites)
cd backend
herd link your-app
herd secure your-app
```

Your app will be available at **https://your-app.test**

> **PHP version:** Right-click the site in Herd → PHP Version → select **8.4**.

---

## Environment Configuration

`.env.example` is committed with Herd Pro defaults pre-filled. After `cp .env.example .env` you should only need to add API keys.

```dotenv
APP_NAME="YourApp"
APP_URL=https://your-app.test

# Database — Herd Pro defaults (no password by default)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_app
DB_USERNAME=root
DB_PASSWORD=

# Redis — Herd Pro default
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Mail — Herd Pro Mailpit (http://localhost:8025)
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=2525

# Meilisearch
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=

# Reverb (websockets)
REVERB_APP_ID=your-app
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST=localhost
REVERB_PORT=8080

# Media (local for dev, S3/R2 for prod)
FILESYSTEM_DISK=local

# Socialite — Google
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=https://your-app.test/auth/google/callback

# Socialite — Apple
APPLE_CLIENT_ID=
APPLE_CLIENT_SECRET=
APPLE_REDIRECT_URI=https://your-app.test/auth/apple/callback

# Stripe — Cashier
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
```

> **OAuth credentials:** Only needed when working on the Auth module. Ask a team lead for shared staging credentials.

---

## Daily Development

Herd Pro manages the PHP server automatically — you don't need `artisan serve`. Just start the supporting services:

```bash
task dev
```

This starts in parallel:
- Vite (frontend hot reload)
- Horizon (queue worker)
- Reverb (websockets)

Individual services:

```bash
task dev:frontend     # Vite HMR only
task dev:queue        # Horizon only
task dev:reverb       # Reverb only
task dev:stripe       # Stripe webhook forwarding (requires Stripe CLI)
```

> **Stripe CLI:** Install via `brew install stripe/stripe-cli/stripe` then `stripe login`. Required when working on the Billing module to receive webhook events locally.

> **Mail:** Herd Pro includes Mailpit. View intercepted emails at [http://localhost:8025](http://localhost:8025)

---

## Database

```bash
task migrate              # run pending migrations
task migrate:fresh        # drop all, remigrate, seed (⚠️ destructive)
```

---

## Testing

```bash
task test                        # all backend tests (Pest 4, parallel)
task test:unit                   # unit tests only
task test:feature                # feature tests only
task test:module NAME=YourModule # single module tests

task test:frontend        # Vitest (run once)
task test:frontend:watch  # Vitest (watch mode)

task test:all             # backend + frontend
```

---

## Scaffolding

### Create a new module

```bash
task module:make NAME=MyModule
```

This scaffolds the full module structure under `backend/modules/MyModule/` and auto-registers the ServiceProvider in `bootstrap/providers.php`.

---

## Project Structure

```
your-app/
├── backend/                    # Laravel 13 application
│   ├── app/
│   │   ├── Workflows/          # Cross-module orchestration only
│   │   └── Support/            # Shared base classes
│   │       ├── Actions/        # Action base class
│   │       ├── Queries/        # Query base class
│   │       ├── DataTransferObjects/
│   │       ├── Snapshots/
│   │       └── ValueObjects/
│   ├── modules/                # Feature modules (add yours here)
│   └── routes/
│       └── shared.php
├── frontend/                   # Vue 3 + TypeScript
│   ├── pages/                  # Inertia page components
│   ├── domains/                # Reusable UI tied to backend concepts
│   ├── components/             # Global primitives
│   └── wayfinder/              # Generated route helpers (never edit manually)
├── public/
├── Taskfile.yml
└── pnpm-workspace.yaml
```

See `backend/module-structure.yml` for the canonical module directory structure.
