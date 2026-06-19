# Vue 3 Design System Implementation Plan — Revised

Corrected copy of original plan. Changes noted inline with ⚠️.

The system is:

* Token-driven (no hardcoded CSS values in app code)
* Fully component-based (no utility classes)
* Strictly structured (no freeform styling props)
* Explicit-import only (no global component registration)
* Headless where appropriate (tables, forms)
* Layout primitives inspired by Every Layout methodology (hand-built, no external package)
* UI primitives wrapped over Web Awesome (isolated via adapters)
* Motion system standardized and token-driven
* Dark mode implemented purely via token mapping

---

# 1. Core Architectural Principles

## 1.1 Hard Rules

* ❌ No Tailwind
* ❌ No utility CSS classes in application code
* ❌ No raw CSS values (px, rem, hex, etc.) in components
* ❌ No direct Web Awesome usage in application code
* ❌ No layout methodology package imports (these are hand-built)
* ❌ No global component registration
* ❌ No arbitrary spacing / sizing / color values
* ❌ No styling props (e.g. `padding="12px"`)
* ❌ No hardcoded route strings — always use Wayfinder (`route()`)
* ❌ No imports from `@awesome.me/webawesome` outside adapter files

---

## 1.2 Allowed Patterns

* ✅ Token-based styling only (`--ds-*` variables)
* ✅ Explicit imports from `@/components`
* ✅ Slot-based composition
* ✅ Headless components (tables, forms)
* ✅ Strict `variant` prop only for UI differences
* ✅ Layout components for structural concerns only
* ✅ `::part()` selectors inside adapter/wrapper `<style scoped>` blocks only

---

## 1.3 System Layers

```
Design Tokens (--ds-* CSS vars → --wa-* CSS vars)
↓
Styles (tokens.css, motion.css, base.css)
↓
Layouts (AppStack, AppCluster, AppGrid, …)
↓
Primitives (AppButton, AppText, AppIcon, … — WA adapters)
↓
Forms (AppForm, AppTextField, AppSelect, …)
↓
Tables (AppDataTable — headless)
↓
Patterns (AppPage, AppCard, AppEmptyState, PremiumGate, …)
↓
Application Pages (developer composed)
```

---

# 2. Folder Structure

⚠️ **Changed from original.** No separate `design-system/` subtree or separate package.json. Everything lives inside `frontend/` using the existing `@` alias. This matches CLAUDE.md structure exactly.

```
frontend/
├── app.ts
├── app.css                     # imports tokens.css, motion.css, base.css
├── styles/
│   ├── tokens.css              # --ds-* vars mapped to --wa-* vars
│   ├── motion.css              # motion tokens + presets
│   └── base.css                # resets, :root theme setup
├── components/
│   ├── index.ts                # barrel — all App* public exports
│   ├── layouts/                # AppStack, AppCluster, AppGrid, AppSidebar,
│   │                           # AppCenter, AppBox, AppCover, AppFrame,
│   │                           # AppReel, AppShell
│   ├── primitives/             # AppButton, AppText, AppIcon, AppBadge,
│   │                           # AppTag, AppAvatar, AppSpinner, AppSkeleton,
│   │                           # AppDivider, AppTooltip, AppDrawer, …
│   ├── forms/                  # AppForm, AppFormSection, AppTextField,
│   │                           # AppSelect, AppCheckbox, AppRadio,
│   │                           # AppSwitch, AppRange, AppTextarea
│   ├── tables/                 # AppDataTable
│   ├── patterns/               # AppPage, AppPageHeader, AppCard,
│   │                           # AppFilterBar, AppEmptyState,
│   │                           # AppConfirmDialog, AppStatCard, PremiumGate
│   └── adapters/               # internal WA wrappers — NOT exported publicly
├── composables/
│   ├── useConfirm.ts
│   ├── useAuth.ts
│   └── useToast.ts
├── domains/                    # module-specific UI (per CLAUDE.md)
├── pages/                      # Inertia pages (per CLAUDE.md)
└── types/
    ├── shared.ts               # SharedProps, AuthUser
    └── ui.ts                   # AppVariant, AppSize, AppPlacement
                                # (no @awesome.me imports)
```

Import pattern:

```typescript
import { AppButton, AppStack, AppTextField } from '@/components'
import { useConfirm } from '@/composables/useConfirm'
import { route } from '@/types'
```

---

# 3. Design Tokens System

## 3.1 Token Categories

### Color Tokens

* `--ds-color-surface`
* `--ds-color-surface-raised`
* `--ds-color-text`
* `--ds-color-text-muted`
* `--ds-color-primary`
* `--ds-color-danger`
* `--ds-color-success`
* `--ds-color-warning`
* `--ds-color-border`
* `--ds-color-focus-ring`

### Spacing Tokens

* `--ds-space-xs`
* `--ds-space-sm`
* `--ds-space-md`
* `--ds-space-lg`
* `--ds-space-xl`

### Typography Tokens

* `--ds-font-sans`
* `--ds-font-mono`
* `--ds-font-size-sm`
* `--ds-font-size-md`
* `--ds-font-size-lg`

### Radius Tokens

* `--ds-radius-sm`
* `--ds-radius-md`
* `--ds-radius-lg`
* `--ds-radius-full`

### Elevation Tokens

* `--ds-shadow-none`
* `--ds-shadow-sm`
* `--ds-shadow-md`
* `--ds-shadow-lg`

---

## 3.2 Theme Strategy

WA 3.7 has **no JS `setTheme()` function**. Theme is purely CSS class-based. The `webawesome.d.ts` exports no theme toggle — verified against installed package.

**How it works:**

* `.wa-theme-awesome` on `<html>` activates the awesome theme (light by default)
* Adding `.wa-dark` to `<html>` switches to dark mode
* Adding `.wa-light` is explicit light (same as default, useful for clarity)

`app.ts` adds `.wa-theme-awesome` once on startup. `useTheme()` composable toggles `.wa-dark` / `.wa-light` and persists to `localStorage`.

```typescript
// composables/useTheme.ts
import type { AppTheme } from '@/types/ui'

const STORAGE_KEY = 'ds-theme'

function apply(theme: AppTheme): void {
  const html = document.documentElement
  if (theme === 'dark') {
    html.classList.add('wa-dark')
    html.classList.remove('wa-light')
  } else {
    html.classList.remove('wa-dark')
    html.classList.add('wa-light')
  }
  localStorage.setItem(STORAGE_KEY, theme)
}

function init(): void {
  const saved = localStorage.getItem(STORAGE_KEY) as AppTheme | null
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  apply(saved ?? (prefersDark ? 'dark' : 'light'))
}

export function useTheme() {
  return { apply, toggle, current, init }
}
```

Themes are implemented via CSS variables only. WA's theme selectors handle light/dark automatically once the classes are on `<html>`.

### Rule

* Components NEVER know theme state
* Theme only changes token values
* Only `useTheme()` touches theme classes — never ad-hoc classList calls elsewhere

---

## 3.3 Token Example Mapping

Actual WA 3.7 token names (verified against installed package).

```css
/* frontend/styles/tokens.css */

:root {
  /* Surfaces */
  --ds-color-surface:          var(--wa-color-surface-default);
  --ds-color-surface-raised:   var(--wa-color-surface-raised);
  --ds-color-surface-lowered:  var(--wa-color-surface-lowered);

  /* Text */
  --ds-color-text:             var(--wa-color-text-normal);
  --ds-color-text-muted:       var(--wa-color-text-quiet);
  --ds-color-text-link:        var(--wa-color-text-link);

  /* Borders & Focus */
  --ds-color-border:           var(--wa-color-surface-border);
  --ds-color-focus:            var(--wa-color-focus);

  /* Primary (brand) — WA 3.x uses fill/border/on semantic variants */
  --ds-color-primary:          var(--wa-color-brand-border-loud);
  --ds-color-primary-fill:     var(--wa-color-brand-fill-loud);
  --ds-color-primary-subtle:   var(--wa-color-brand-fill-quiet);
  --ds-color-on-primary:       var(--wa-color-brand-on-loud);

  /* Spacing — WA uses xs/s/m/l/xl (NOT x-small/small/medium) */
  --ds-space-xs:   var(--wa-space-xs);   /* 8px  */
  --ds-space-sm:   var(--wa-space-s);    /* 12px */
  --ds-space-md:   var(--wa-space-m);    /* 16px */
  --ds-space-lg:   var(--wa-space-l);    /* 24px */
  --ds-space-xl:   var(--wa-space-xl);   /* 32px */
  --ds-space-2xl:  var(--wa-space-2xl);  /* 40px */

  /* Typography — WA uses xs/s/m/l/xl (NOT x-small/small/medium) */
  --ds-font-size-xs:  var(--wa-font-size-xs);  /* 12px */
  --ds-font-size-sm:  var(--wa-font-size-s);   /* 14px */
  --ds-font-size-md:  var(--wa-font-size-m);   /* 16px */
  --ds-font-size-lg:  var(--wa-font-size-l);   /* 20px */
  --ds-font-size-xl:  var(--wa-font-size-xl);  /* 25px */

  /* Radius — WA uses s/m/l (NOT small/medium/large) */
  --ds-radius-sm:    var(--wa-border-radius-s);
  --ds-radius-md:    var(--wa-border-radius-m);
  --ds-radius-lg:    var(--wa-border-radius-l);
  --ds-radius-full:  var(--wa-border-radius-pill);

  /* Elevation — WA has no named shadow tokens; use color-mix approach */
  --ds-shadow-sm: 0 1px 2px color-mix(in oklab, var(--wa-color-text-normal) 8%, transparent);
  --ds-shadow-md: 0 4px 8px color-mix(in oklab, var(--wa-color-text-normal) 12%, transparent);
  --ds-shadow-lg: 0 8px 24px color-mix(in oklab, var(--wa-color-text-normal) 16%, transparent);
}
```

---

# 4. Motion System

## 4.1 Motion Tokens

WA 3.7 actual transition token names (verified): `--wa-transition-fast` / `--wa-transition-normal` / `--wa-transition-slow`. There is no `--wa-transition-medium`.

WA already sets all three to `0` under `prefers-reduced-motion` internally — we mirror that for our own `--ds-duration-*` tokens.

```css
/* frontend/styles/motion.css */

:root {
  --ds-duration-fast:    120ms;
  --ds-duration-normal:  200ms;
  --ds-duration-slow:    300ms;

  --ds-ease-standard:    cubic-bezier(0.4, 0, 0.2, 1);
  --ds-ease-emphasized:  cubic-bezier(0.2, 0, 0, 1);
  --ds-ease-accelerate:  cubic-bezier(0.3, 0, 1, 1);
  --ds-ease-decelerate:  cubic-bezier(0, 0, 0, 1);

  /* Keep WA components in sync with DS motion tokens */
  --wa-transition-fast:   var(--ds-duration-fast);
  --wa-transition-normal: var(--ds-duration-normal);
  --wa-transition-slow:   var(--ds-duration-slow);
}

@media (prefers-reduced-motion: reduce) {
  :root {
    --ds-duration-fast:   0ms;
    --ds-duration-normal: 0ms;
    --ds-duration-slow:   0ms;
  }
}
```

---

## 4.2 Motion Presets

Defined as named Vue `<Transition>` name values and corresponding `@keyframes`:

* `overlay-in` / `overlay-out`
* `menu-in` / `menu-out`
* `page-enter` / `page-exit`

---

## 4.3 Motion Rules

* No inline animation definitions
* No component-defined timing values
* All motion uses `--ds-duration-*` and `--ds-ease-*` tokens
* WA component animation tokens (`--wa-transition-*`) mapped to DS tokens at `:root`

---

## 4.4 Accessibility Rule

Respect `prefers-reduced-motion`:

```css
@media (prefers-reduced-motion: reduce) {
  :root {
    --ds-duration-fast:   0ms;
    --ds-duration-normal: 0ms;
    --ds-duration-slow:   0ms;
  }
}
```

Setting durations to 0ms via tokens handles both custom and WA transitions in one rule.

---

# 5. Layout System (Every Layout–Inspired)

⚠️ **Naming clarified.** These are custom-built components inspired by the Every Layout methodology (Andy Bell / Heydon Pickering). There is no external npm package — these are hand-written.

## 5.1 Purpose

Layout components handle structure only:

* spacing
* alignment
* flow
* responsive structure

They do NOT handle:

* color
* typography
* business logic

---

## 5.2 Layout Components

* `AppStack` — vertical flow with gap
* `AppCluster` — horizontal wrapping group
* `AppGrid` — equal-column grid
* `AppSidebar` — main + fixed-width aside
* `AppSwitcher` — horizontal until breakpoint, then stack
* `AppCenter` — horizontally centered container
* `AppBox` — padded container
* `AppCover` — vertically centered hero area
* `AppFrame` — aspect-ratio enforcer
* `AppReel` — horizontal scroll container
* `AppShell` — full-page skeleton (sidebar + header + main)

---

## 5.3 Example: AppStack

```vue
<template>
  <div className="ds-stack" :data-space="space">
    <slot/>
  </div>
</template>
```

```css
.ds-stack {
  display: flex;
  flex-direction: column;
}

.ds-stack[data-space="sm"] > * + * { margin-top: var(--ds-space-sm); }
.ds-stack[data-space="md"] > * + * { margin-top: var(--ds-space-md); }
.ds-stack[data-space="lg"] > * + * { margin-top: var(--ds-space-lg); }
```

---

## 5.4 AppShell (Slot-Based)

```vue
<AppShell>
  <template #sidebar/>
  <template #header/>
  <template #default/>
</AppShell>
```

* AppShell is layout-only
* No navigation logic inside design system

---

# 6. Primitive Components (Web Awesome Adapter Layer)

## 6.1 Rules

* All primitives wrap Web Awesome via adapter files in `components/adapters/`
* Adapters are NOT exported — only the `App*` component is public
* Web Awesome never exposed directly to application code
* All props normalized to `AppVariant`, `AppSize`, `AppPlacement` types from `types/ui.ts`
* All events re-emitted as standard Vue events (no `wa-*` event names in application code)
* `::part()` selectors allowed only inside adapter `<style scoped>` blocks

---

## 6.2 TypeScript Types (no WA imports)

```typescript
// frontend/types/ui.ts
export type AppVariant = 'primary' | 'secondary' | 'success' | 'warning' | 'danger' | 'neutral'
export type AppSize = 'sm' | 'md' | 'lg'
export type AppPlacement = 'top' | 'bottom' | 'left' | 'right' | 'top-start' | 'top-end'
```

---

## 6.3 Example: AppButton

```vue
<template>
  <wa-button :variant="variant" :size="size" :loading="loading" :disabled="disabled || loading">
    <slot/>
  </wa-button>
</template>

<script setup lang="ts">
import type { AppVariant, AppSize } from '@/types/ui'

const props = withDefaults(defineProps<{
  variant?: AppVariant
  size?: AppSize
  loading?: boolean
  disabled?: boolean
}>(), {
  variant: 'primary',
  size: 'md',
})
</script>
```

---

## 6.4 AppText

⚠️ **Tag prop added.** Without it, all text renders as the same element — breaks semantic HTML and accessibility.

```vue
<AppText tag="h1" variant="page-title">Dashboard</AppText>
<AppText tag="p" variant="body">Description</AppText>
<AppText tag="span" variant="caption">12 items</AppText>
```

```typescript
defineProps<{
  tag?: 'h1' | 'h2' | 'h3' | 'h4' | 'p' | 'span' | 'label' | 'legend'
  variant: 'page-title' | 'section-title' | 'body' | 'caption'
}>()
```

---

## 6.5 Example: AppDialog

* Slot-based composition
* Motion preset driven
* No inline animation logic
* Wired to `useConfirm()` composable for programmatic use

```
AppDialog
  #header slot
  #default slot (body)
  #footer slot
```

---

# 7. Forms (Field-Based System)

## 7.1 Rules

* No schema-driven forms
* No auto-generated forms
* Fully field-based composition
* Every field integrates with Inertia `useForm()` via `form` + `field` props
* Errors sourced from `AppResponse` shape: `errors: Record<string, string[]>`

---

## 7.2 Structure

```
AppForm
  AppFormSection
    AppFormSectionHeader
    AppFormSectionBody
```

---

## 7.3 Field Requirements

Every field supports:

* `label`
* `form` + `field` props (Inertia `useForm()` binding)
* Error display — auto-reads `form.errors[field]`, rendered via help-text slot
* Auto-clears error on input via `form.clearErrors(field)`
* `help` text (non-error)
* `required` state
* `disabled` state
* Consistent spacing via layout tokens

---

## 7.4 Form Field Binding Pattern

⚠️ **Added.** All form fields follow this pattern:

```vue
<AppForm @submit.prevent="form.post(route('items.store'))">
  <AppFormSection>
    <AppTextField :form="form" field="name" label="Name"/>
    <AppSelect    :form="form" field="role" label="Role">
      <option value="admin">Admin</option>
      <option value="member">Member</option>
    </AppSelect>
  </AppFormSection>
</AppForm>
```

Internal implementation:

```typescript
// Any AppTextField / AppSelect / AppCheckbox etc.
const props = defineProps<{
  form: ReturnType<typeof useForm>   // Inertia useForm instance
  field: string
  label: string
  required?: boolean
  disabled?: boolean
  help?: string
}>()

const value = computed(() => props.form[props.field])
const error = computed(() => props.form.errors[props.field])

function onInput(val: unknown) {
  props.form[props.field] = val
  props.form.clearErrors(props.field)
}
```

---

## 7.5 Example

```vue
<AppForm @submit.prevent="form.post(route('items.store'))">
  <AppFormSection>
    <AppTextField  :form="form" field="name"  label="Name" required="true"/>
    <AppTextField  :form="form" field="email" label="Email" type="email"/>
    <AppSelect     :form="form" field="role"  label="Role">
      <option value="admin">Admin</option>
    </AppSelect>
    <AppCheckbox   :form="form" field="active" label="Active"/>
  </AppFormSection>
  <AppButton type="submit" :loading="form.processing">Save</AppButton>
</AppForm>
```

---

# 8. Tables (Headless System)

## 8.1 Rules

* Table is headless
* Developer controls data and rendering
* System controls UX behavior (sorting, pagination, empty/loading states)
* Pagination integrates with `AppResponse` `meta` shape
* Sorting integrates with Inertia `router.get()` — server-side by default

---

## 8.2 API

```vue
<AppDataTable
  :rows="rows"
  :columns="columns"
  :meta="page.props.meta"
  :loading="false"/>
```

---

## 8.3 Columns

```typescript
const columns: TableColumn[] = [
  { key: 'name',  label: 'Name',  sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'role',  label: 'Role' },
]
```

---

## 8.4 Slots

```vue
<template #cell.actions="{ row }">
  <AppButton variant="secondary" @click="edit(row)">Edit</AppButton>
</template>
```

---

## 8.5 Server-Side Sorting and Pagination

⚠️ **Added.** CRUD app on Laravel/Inertia means server-side operations. AppDataTable must not implement client-side sort/page.

AppDataTable fires events; parent handles Inertia navigation:

```vue
<AppDataTable
  :rows="rows"
  :columns="columns"
  :meta="page.props.meta"
  @sort="onSort"
  @paginate="onPage"/>
```

```typescript
function onSort({ key, direction }: { key: string; direction: 'asc' | 'desc' }) {
  router.get(
    route('items.index'),
    { sort: key, direction, page: 1 },
    { preserveScroll: true, preserveState: true }
  )
}

function onPage(page: number) {
  router.get(
    route('items.index'),
    { ...currentFilters, page },
    { preserveScroll: true, preserveState: true }
  )
}
```

AppDataTable reads `meta` (from `AppResponse` paginated shape) to render pagination controls:

```typescript
interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}
```

---

## 8.6 System Responsibilities

* sorting UI (fires `sort` event — does not navigate itself)
* pagination controls (fires `paginate` event — does not navigate itself)
* empty states
* loading/skeleton states
* row density
* accessibility (roles, aria-sort)

---

# 9. Patterns Layer

Reusable compositions (not business-specific).

### Page Patterns

* `AppPage`
* `AppPageHeader`
* `AppPageTitle`
* `AppPageActions`
* `AppPageContent`
* `AppPageSection`

### Card Patterns

* `AppCard`
* `AppCardHeader`
* `AppCardBody`
* `AppCardFooter`

### Utility Patterns

* `AppFilterBar`
* `AppSearchInput`
* `AppEmptyState`
* `AppConfirmDialog`
* `AppStatCard`

### Gating

* `PremiumGate` — ⚠️ **Added.** Required by CLAUDE.md. Wraps any premium feature. Reads `page.props.auth.user.is_premium` from shared props.

```vue
<PremiumGate>
  <PremiumFeatureComponent/>
</PremiumGate>
```

---

# 10. Composition Rules

⚠️ **Rule corrected.** Original rule was backwards — it forbade layout primitives inside UI components, which makes them useless.

## Correct Rule

**Application code** composes using named sub-components, not raw layout primitives inside UI shells:

```vue
<!-- ✅ Application code: use named sub-components -->
<AppCard>
  <AppCardHeader>Title</AppCardHeader>
  <AppCardBody>Content</AppCardBody>
</AppCard>

<!-- ❌ Application code: don't reach past the API -->
<AppCard>
  <AppStack space="md">Content</AppStack>
</AppCard>
```

**Design system internals** (inside `AppCardBody` itself) MAY use layout primitives freely:

```vue
<!-- ✅ Inside AppCardBody.vue — layout primitives are fine here -->
<template>
  <AppStack space="md">
    <slot/>
  </AppStack>
</template>
```

Rule in one sentence: **application code uses the design system's public API; the design system uses layout primitives internally.**

---

# 11. Import Strategy

## Explicit imports only

```typescript
import { AppButton, AppTextField, AppStack, AppDataTable } from '@/components'
import { useConfirm } from '@/composables/useConfirm'
import { route } from '@/types'
```

## No global registration

* No `app.use(registerAll)`
* No automatic Vue plugin injection

---

# 12. Adapter Layer

## Purpose

Isolate Web Awesome. Files in `components/adapters/` are the only place that imports from `@awesome.me/webawesome`.

## Responsibilities

* normalize props to `AppVariant` / `AppSize` / `AppPlacement`
* re-emit WA events (`wa-change`, `wa-input`) as standard Vue events
* map `--ds-*` tokens → `--wa-*` vars at `:root` (tokens.css handles this)
* contain all `::part()` selector usage
* allow full WA replacement by rewriting adapters only

---

# 13. ESLint / Enforcement Rules

Must enforce:

* no raw `px` / `rem` / hex values in Vue `<style>` blocks
* no `--wa-*` variable references outside `styles/tokens.css` and `components/adapters/`
* no `wa-` tag names outside `components/adapters/`
* no imports from `@awesome.me/webawesome` outside `components/adapters/`
* no hardcoded route strings — Wayfinder only
* no relative imports — `@/` alias only

---

# 14. Composables

Live in `frontend/composables/` per CLAUDE.md. Design system composables that ship with the system:

| Composable     | Purpose                                              |
| -------------- | ---------------------------------------------------- |
| `useConfirm()` | Programmatic `AppConfirmDialog` API                  |
| `useToast()`   | Programmatic toast via `AppToast`                    |
| `useTheme()`   | Theme toggle — syncs `data-theme` + `wa-set-theme()` |

Application composables (`useAuth`, etc.) also live in `frontend/composables/` but are not part of the design system layer.

---

# 15. Testing

⚠️ **Added.** Design system component tests live at:

```
frontend/components/primitives/__tests__/
frontend/components/forms/__tests__/
frontend/components/tables/__tests__/
frontend/components/patterns/__tests__/
```

Run with `task test:frontend`. Uses Vitest + `@vue/test-utils`.

---

# 16. Build Order (Critical Path)

## Phase 1: Foundation

* `types/ui.ts` — AppVariant, AppSize, AppPlacement
* `styles/tokens.css` — full `--ds-*` → `--wa-*` map
* `styles/motion.css` — motion tokens + WA override
* `styles/base.css` — resets, `:root` theme setup
* `useTheme()` composable
* ESLint rules

## Phase 2: Layouts

* `AppStack`, `AppCluster`, `AppGrid`
* `AppShell`

## Phase 3: Primitives

* `AppButton`, `AppText`, `AppIcon`, `AppBadge`
* `AppDialog` + `useConfirm()`
* `AppToast` + `useToast()`

## Phase 4: Forms

* `AppForm`, `AppFormSection`
* `AppTextField`, `AppTextarea`, `AppSelect`
* `AppCheckbox`, `AppRadio`, `AppRadioGroup`, `AppSwitch`

## Phase 5: Tables

* `AppDataTable` (headless core + server-side pagination)

## Phase 6: Patterns

* `AppPage` system
* `AppCard` system
* `AppEmptyState`, `AppFilterBar`, `AppStatCard`
* `PremiumGate`

## Phase 7: First Real App

* Users CRUD module
* Organizations CRUD module

---

# 17. Validation Criteria

System is successful when:

* No component uses raw CSS values
* No layout requires custom CSS outside the design system
* CRUD pages are fully composed without writing new CSS
* Web Awesome can be swapped by rewriting `components/adapters/` only
* Theme change requires zero component changes
* DataTable and form components are reused without modification across all CRUD modules
* No `wa-*` tags appear outside adapter files (ESLint enforces)
* All navigation uses Wayfinder — no hardcoded strings

---

# End State

This design system becomes:

* a constrained UI language for this app
* a composition engine for CRUD modules
* a token-driven theming system synced with WA
* a motion-consistent interaction layer
* a fully isolated vendor abstraction layer (WA replaceable via adapters only)
