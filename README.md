# Platform

A personal application skeleton for building production-ready Laravel + Inertia + Vue apps. Not a framework, not a package — an opinionated starting point that carries hard-won architectural decisions so new projects don't start from zero.

---

## Philosophy

Most projects share the same problems: where does business logic live, how do modules talk to each other, what does a controller actually own, how do you keep things testable. This skeleton answers those questions once and enforces the answers mechanically.

The architecture is a **modular monolith** — one deployable unit, internally structured like microservices. Modules own their data, expose contracts, and never reach into each other's internals. Cross-module communication happens through events and snapshots, never shared models.

Key beliefs this project encodes:

- **Contracts over concretions.** Controllers and workflows depend on interfaces, never concrete classes. Swapping implementations doesn't require touching callers.
- **DTOs at every boundary.** No raw arrays into actions, no Eloquent models out of actions. Data has a known shape everywhere.
- **Enforcement over convention.** PHPStan, Pest architecture tests, and ESLint enforce the rules automatically. Violations fail CI, not code review.
- **Thin controllers.** A controller is a request adapter. It validates input, builds a DTO, calls a contract, and returns a response. Nothing else.
- **One response shape.** Every response — API or Inertia — uses the same envelope via `app_response()`. The frontend always knows what it's getting.

The full architecture reference lives in `CLAUDE.md`. If you're building on top of this, read it before writing a line of code.

---

## Using This Template

### Creating a new project

This repo is a GitHub Template. On the repo page, click **Use this template → Create a new repository**. Name it after your project. You get a clean copy with no shared git history.

After creating your project:

```bash
git clone git@github.com:you/your-new-project.git
cd your-new-project

# Add platform as a remote so you can pull future updates
git remote add platform git@github.com:you/platform.git
git fetch platform

# Tag the platform version you started from
git tag platform-base-v1.0.0
git push origin platform-base-v1.0.0
```

### Pulling platform updates into an existing project

When platform adds something worth having — a new CI step, a security fix, a new architectural pattern:

```bash
git fetch platform
git log platform/main --oneline   # see what's new since your tag

# Pull a specific commit (safest)
git cherry-pick <commit-hash>

# Or merge everything (use sparingly, resolve conflicts in modules/ in favour of your project)
git merge platform/main
```

### What to rename after creating a project

- `APP_NAME` in `.env`
- Database name in `.env` (`DB_DATABASE`)
- Herd site name in `Taskfile.yml` (`setup:herd` task)
- `APP_URL` in `.env`
- The root `package.json` `name` field in `frontend/`

---

## What's Included

### Backend
- **Laravel 13** with PHP 8.4 strict types throughout
- **Modular monolith** structure — `php artisan make:module Name` scaffolds the full DDD layout
- **Contract-driven architecture** — actions, queries, and cross-module communication all interface-bound
- **AppResponse** — unified JSON/Inertia response envelope via `app_response()` helper
- **Laravel Horizon** — queue management with `default` and `high` supervisors configured
- **Laravel Reverb** — websockets, ready for local and production
- **Laravel Cashier + Stripe** — billing and subscription management wired up
- **Laravel Scout + Meilisearch** — full-text search
- **Spatie Media Library** — file/media handling with S3/R2 support
- **Spatie Laravel Permission** — roles and permissions
- **Laravel Pennant** — feature flags
- **Laravel Socialite** — OAuth (Google, Apple pre-configured)
- **Filament** — admin panel
- **Sentry** — error tracking (backend)

### Frontend
- **Vue 3 + TypeScript** with strict type checking
- **Inertia 3** — no API layer needed, Inertia handles the bridge
- **WebAwesome** — UI component library (`wa-button`, `wa-card`, etc.)
- **Wayfinder** — type-safe route generation, no hardcoded URL strings
- **Vitest** — frontend unit testing
- **Oxlint + Oxfmt** — fast Rust-based linting and formatting
- **Vite 8** — asset bundling with vendor chunk splitting (Vue, Inertia, WebAwesome split separately)
- **Sentry** — error tracking (frontend)

### CI/CD
- **GitHub Actions** — two workflows:
  - `ci.yml` — PHP (Pint, PHPStan, Pest) + frontend (type-check, lint, format, Vitest, build) + security audits on every PR and push to `main`
  - `deploy.yml` — triggers Laravel Forge deploy webhook after CI passes on `main`
- **PHPStan level 8** with Larastan, baseline committed
- **Pest architecture tests** — module isolation, controller boundaries, workflow placement, final classes, immutability, strict types all enforced automatically

### Security
- **Security headers middleware** — CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy on every response
- **CORS** configured (`CORS_ALLOWED_ORIGINS` env var)
- **Rate limiting** — named `api` (60/min) and `auth` (5/min) limiters defined, ready to apply to routes

---

## Tech Stack

| Layer           | Choice                                      |
| --------------- | ------------------------------------------- |
| Backend         | Laravel 13, PHP 8.4                         |
| Frontend        | Inertia 3 + Vue 3 + TypeScript + WebAwesome |
| Database        | MySQL 8 + Redis                             |
| Search          | Meilisearch                                 |
| Media           | Spatie Media Library + S3/R2                |
| Queues          | Laravel Horizon                             |
| Websockets      | Laravel Reverb                              |
| Payments        | Laravel Cashier + Stripe                    |
| Admin           | Filament                                    |
| Error tracking  | Sentry                                      |
| Package manager | pnpm                                        |
| Task runner     | Task (taskfile.dev)                         |
| CI/CD           | GitHub Actions + Laravel Forge              |
| Mobile (opt.)   | NativePHP                                   |

---

## Prerequisites

| Tool | Version | Install |
| ---- | ------- | ------- |
| [Laravel Herd Pro](https://herd.laravel.com) | Latest | Download from herd.laravel.com |
| [pnpm](https://pnpm.io) | 9+ | `brew install pnpm` |
| [Task](https://taskfile.dev) | 3+ | `brew install go-task` |

**Herd Pro is required** — it bundles PHP 8.4, MySQL 8, Redis, and Mailpit. The free tier does not include MySQL or Redis.

Once Herd Pro is running, verify MySQL and Redis show as active in the Herd menubar app under **Services** before proceeding.

### Meilisearch

Not bundled with Herd Pro, install separately:

```bash
brew install meilisearch
brew services start meilisearch
```

Verify at [http://localhost:7700](http://localhost:7700).

---

## Getting Started

### 1. Create the database

Open **Herd Pro → Database** (or TablePlus) and create a database with the same name as your `DB_DATABASE` env value.

### 2. Run setup

```bash
task setup
```

This runs in sequence:

```bash
task setup:backend    # composer install → cp .env.example .env → key:generate → migrate + seed → storage:link
task setup:frontend   # pnpm install → build
task setup:herd       # herd link + herd secure
```

> **PHP version:** Right-click the site in Herd → PHP Version → select **8.4**.

Your app will be available at `https://your-app.test`.

### 3. Manual steps (if setup fails or you prefer)

```bash
# Root — .env lives at the monorepo root
cp .env.example .env

# Backend
cd backend
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

# Frontend
cd frontend
pnpm install
pnpm build

# Herd — run from monorepo root (public/ lives here)
herd link your-app
herd secure your-app
```

---

## Environment Configuration

`.env.example` is committed with sensible local defaults. After copying, the minimum you need to change for local dev:

```dotenv
APP_NAME="YourApp"
APP_URL=https://your-app.test
DB_DATABASE=your_app

# Redis (Herd Pro defaults)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Mail — Herd Mailpit (view at http://localhost:8025)
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=2525

# Meilisearch
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
```

The `.env.example` file contains commented production hardening values at the bottom — review these before going live.

---

## Daily Development

Herd Pro manages the PHP server automatically. Just start the supporting services:

```bash
task dev
```

Starts in parallel: Vite (HMR), Horizon (queues), Reverb (websockets).

Individual services:

```bash
task dev:frontend     # Vite only
task dev:queue        # Horizon only
task dev:reverb       # Reverb only
task dev:stripe       # Stripe webhook forwarding (requires Stripe CLI: brew install stripe/stripe-cli/stripe)
```

Mail is intercepted by Herd's Mailpit — view at [http://localhost:8025](http://localhost:8025).

---

## Testing

```bash
task test                          # all backend tests (Pest, parallel)
task test:unit                     # unit only
task test:feature                  # feature only
task test:module NAME=YourModule   # single module

task test:frontend                 # Vitest (once)
task test:frontend:watch           # Vitest (watch)
task test:all                      # backend + frontend
```

---

## Scaffolding

```bash
task module:make NAME=MyModule   # scaffold full module under backend/modules/MyModule/
```

See `backend/module-structure.yml` for the canonical module directory layout, and the skills in `.claude/` for step-by-step guides on creating actions, queries, controllers, and workflows.

---

## Key Commands

```bash
task migrate              # run pending migrations
task migrate:fresh        # drop all + remigrate + seed (⚠️ destructive)
task lint                 # PHPStan static analysis
task type-check           # TypeScript type check
task commit               # run all checks before committing
```

Full command reference in `Taskfile.yml`.

---

## Project Structure

```
platform/
├── .github/workflows/      # CI (ci.yml) and deploy (deploy.yml)
├── backend/                # Laravel 13
│   ├── app/
│   │   ├── Contracts/      # Shared interfaces (Action, Query, Snapshot, VO, etc.)
│   │   ├── Data/           # Shared enums (HttpResponse)
│   │   ├── Http/
│   │   │   ├── Middleware/ # HandleInertiaRequests, SecurityHeaders
│   │   │   └── Responses/  # AppResponseFactory, ApiResponse, InertiaResponse
│   │   ├── Providers/      # AppServiceProvider, FeatureServiceProvider, etc.
│   │   ├── Support/        # helpers.php (app_response()), base classes
│   │   └── Workflows/      # Cross-module orchestration (empty until you add it)
│   ├── modules/            # Your feature modules live here
│   ├── config/
│   ├── routes/
│   └── tests/
│       └── Architecture/   # Automated architecture enforcement tests
├── frontend/               # Vue 3 + TypeScript
│   ├── pages/              # Inertia page components
│   ├── domains/            # Reusable UI tied to backend modules
│   ├── components/         # Global primitives
│   ├── composables/        # Cross-cutting Vue composables
│   └── types/              # Wayfinder-generated route types (never edit)
├── public/                 # Laravel public dir (monorepo root)
├── .env.example
└── Taskfile.yml
```
