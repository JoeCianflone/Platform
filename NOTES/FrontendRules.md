## Package Structure

* `frontend/` is a pnpm workspace root — never put app code here directly
* `frontend/ds/` → `@platform/ds` — the design system library
* `frontend/src/` → `@platform/app` — the application
* All tooling devDeps live in the workspace root `frontend/package.json`
* Runtime deps (`vue`, `@awesome.me/webawesome`, `@inertiajs/vue3`) live in `src/package.json`
* `@platform/ds` declares `vue` and `@awesome.me/webawesome` as `peerDependencies` only

## Aliases

* `@ds` → `frontend/ds/` — used in BOTH packages
* `@` → `frontend/src/` — used in `src/` only
* Never use relative paths (`../`) anywhere — oxlint enforces this

## Naming Conventions

* Components in `ds/` → `Plat*` prefix (e.g. `PlatButton`, `PlatConfirmDialog`)
* Components in `src/` → `App*` prefix (e.g. `AppPremiumGate`, `AppToast`)
* Composables in `ds/` → `usePlat*` prefix (e.g. `usePlatToast`, `usePlatTheme`)
* Composables in `src/` → `use*` prefix with no restriction (e.g. `useAuth`, `useToast` is safe — won't collide)
* Component filenames match their export name exactly

## What Lives in ds/

* WA-wrapping components (all `Plat*`)
* Pure Vue composables with no Inertia dependency (`usePlatToast`, `usePlatConfirm`, `usePlatTheme`)
* Design tokens CSS (`styles/tokens.css`, `styles/motion.css`, `styles/base.css`)
* DS-level types (`types/ui.ts` — `AppVariant`, `AppSize`, `AppPlacement`, `AppTheme`, `InertiaForm`, `TableColumn`, `PaginationMeta`)
* `index.ts` barrel — single entry point for everything in `ds/`

## What Lives in src/

* Inertia-coupled components (anything that imports from `@inertiajs/vue3`)
* Application entry point (`app.ts`, `app.css`)
* Pages, domains, app-level composables
* `types/shared.ts` — `SharedProps`, `AuthUser` (app-specific, writable)
* `types/wayfinder/` — Wayfinder output (never edit manually)

## The Inertia Rule

A component belongs in `src/` if and only if it imports from `@inertiajs/vue3`.
Everything else belongs in `ds/`.

Current `src/` components because of this rule:

* `AppToast.vue` — watches `usePage()` for flash/errors
* `AppPremiumGate.vue` — reads `usePage()` for `auth.user.is_premium`

## WA Import Rule

* `@awesome.me/webawesome` imports allowed only in `ds/components/**`
* `src/` never imports WA directly — use `Plat*` components from `@ds` instead
* `src/components/AppToast.vue` is the one exception (needs the `wa-callout` import) — oxlint override applied

## CSS Import Rule

* `src/app.css` imports DS styles via `@ds` alias: `@import '@ds/styles/tokens.css'`
* Never duplicate token definitions between `ds/styles/` and `src/styles/`
* `src/styles/` is for app-level overrides only

## TypeScript Rules

* `ds/tsconfig.app.json` paths: `@ds/*` → `./*`
* `src/tsconfig.app.json` paths: `@/*` → `./*`, `@ds/*` → `../ds/*`
* `src/` includes `types/**/*.ts` and `types/**/*.d.ts` (covers Wayfinder output)
* `ds/` does NOT include Inertia types

## Wayfinder

* Output path: `frontend/src/types/wayfinder/`
* Command: `php artisan wayfinder:generate --path=../frontend/src/types/wayfinder`
* Never edit files in `src/types/wayfinder/` manually — regenerated on every run
* `src/types/shared.ts` is the writable app types file

## ds/index.ts Barrel

* All `Plat*` components exported from barrel
* All `usePlat*` composables exported from barrel
* All DS types re-exported from barrel
* `src/` code may import directly from `@ds/composables/usePlatToast` or from the barrel `@ds` — both work
* Prefer direct path imports for tree-shaking; prefer barrel for convenience in pages

## pnpm Workspace Rules

* `pnpm install` always from `frontend/` (workspace root)
* `task frontend:add` → adds to `@platform/app`
* `task frontend:ds:add` → adds to `@platform/ds`
* Never use `npm` or `yarn`

## Taskfile Commands

* `task frontend:dev` → Vite dev server (runs in `@platform/app`)
* `task frontend:build` → type-check + Vite build (runs in `@platform/app`)
* `task frontend:lint` → oxlint both packages separately
* `task frontend:type-check` → vue-tsc in `@platform/app`
* `task backend:build:routes` → regenerates Wayfinder output
