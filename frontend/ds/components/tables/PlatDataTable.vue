<script lang="ts" setup>
// Imports ............................................................
import '@awesome.me/webawesome/dist/components/spinner/spinner.js'
import '@awesome.me/webawesome/dist/components/skeleton/skeleton.js'
import { ref } from 'vue'
import type { TableColumn, PaginationMeta } from '@ds/types/ui'

// Types & Interfaces .................................................
type TableRow = Record<string, unknown>

// Props & Emits ......................................................
const props = withDefaults(
  defineProps<{
    rows: TableRow[]
    columns: TableColumn[]
    meta?: PaginationMeta
    loading?: boolean
    skeletonRows?: number
  }>(),
  { loading: false, skeletonRows: 5 }
)
const emit = defineEmits<{
  sort: [{ key: string; direction: 'asc' | 'desc' }]
  paginate: [page: number]
}>()

// State ..............................................................
const sortKey = ref<string | null>(null)
const sortDirection = ref<'asc' | 'desc'>('asc')

// Methods / Event Handlers ...........................................
function handleSort(key: string): void {
  if (sortKey.value === key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortDirection.value = 'asc'
  }
  emit('sort', { key: sortKey.value, direction: sortDirection.value })
}
function sortIndicator(key: string): string {
  if (sortKey.value !== key) return '↕'
  return sortDirection.value === 'asc' ? '↑' : '↓'
}
function ariaSort(key: string): 'ascending' | 'descending' | 'none' {
  if (sortKey.value !== key) return 'none'
  return sortDirection.value === 'asc' ? 'ascending' : 'descending'
}

defineExpose({ sortKey, sortDirection })
</script>

<template>
  <div class="ds-table-wrapper">
    <div v-if="loading && rows.length > 0" class="ds-table-loading-bar">
      <wa-spinner />
    </div>

    <div class="ds-table-scroll">
      <table class="ds-table">
        <thead>
          <tr>
            <th
              v-for="col in columns"
              :key="col.key"
              class="ds-table__th"
              :class="{ 'ds-table__th--sortable': col.sortable }"
              :aria-sort="ariaSort(col.key)"
              @click="col.sortable && handleSort(col.key)"
            >
              <span class="ds-table__th-content">
                {{ col.label }}
                <span v-if="col.sortable" class="ds-table__sort-icon" aria-hidden="true">
                  {{ sortIndicator(col.key) }}
                </span>
              </span>
            </th>
          </tr>
        </thead>

        <tbody>
          <template v-if="loading && rows.length === 0">
            <tr v-for="n in skeletonRows" :key="n" class="ds-table__skeleton-row">
              <td v-for="col in columns" :key="col.key" class="ds-table__td">
                <wa-skeleton effect="sheen" />
              </td>
            </tr>
          </template>

          <template v-else-if="rows.length === 0">
            <tr>
              <td :colspan="columns.length" class="ds-table__td ds-table__td--empty">
                <span class="ds-table-empty">No data available.</span>
              </td>
            </tr>
          </template>

          <template v-else>
            <tr v-for="(row, i) in rows" :key="i" class="ds-table__row">
              <td v-for="col in columns" :key="col.key" class="ds-table__td">
                {{ row[col.key] }}
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div v-if="meta && meta.last_page > 1" class="ds-table-pagination">
      <span>{{ meta.from }}–{{ meta.to }} of {{ meta.total }}</span>
      <div class="ds-table-pagination__controls">
        <button
          class="ds-table-pagination__btn"
          :disabled="meta.current_page <= 1"
          @click="emit('paginate', meta.current_page - 1)"
        >
          ‹
        </button>
        <span class="ds-table-pagination__pages"
          >{{ meta.current_page }} / {{ meta.last_page }}</span
        >
        <button
          class="ds-table-pagination__btn"
          :disabled="meta.current_page >= meta.last_page"
          @click="emit('paginate', meta.current_page + 1)"
        >
          ›
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.ds-table-wrapper {
  position: relative;
  border: 1px solid var(--ds-color-border);
  border-radius: var(--ds-radius-md);
  overflow: hidden;
}

.ds-table-loading-bar {
  position: absolute;
  top: var(--ds-space-sm);
  right: var(--ds-space-sm);
  z-index: 1;
}

.ds-table-scroll {
  overflow-x: auto;
}

.ds-table {
  width: 100%;
  border-collapse: collapse;
  font-size: var(--ds-font-size-sm);
}

.ds-table__th {
  padding: var(--ds-space-sm) var(--ds-space-md);
  text-align: left;
  font-weight: 600;
  font-size: var(--ds-font-size-xs);
  color: var(--ds-color-text-muted);
  background-color: var(--ds-color-surface-raised);
  border-bottom: 1px solid var(--ds-color-border);
  white-space: nowrap;
  user-select: none;
}

.ds-table__th--sortable {
  cursor: pointer;
}

.ds-table__th--sortable:hover {
  color: var(--ds-color-text);
}

.ds-table__th-content {
  display: flex;
  align-items: center;
  gap: var(--ds-space-xs);
}

.ds-table__sort-icon {
  color: var(--ds-color-text-muted);
  font-size: var(--ds-font-size-xs);
}

.ds-table__row:hover {
  background-color: var(--ds-color-surface-raised);
}

.ds-table__td {
  padding: var(--ds-space-sm) var(--ds-space-md);
  border-bottom: 1px solid var(--ds-color-border);
  color: var(--ds-color-text);
}

.ds-table__td--empty {
  text-align: center;
  padding: var(--ds-space-2xl) var(--ds-space-md);
}

.ds-table__skeleton-row .ds-table__td {
  padding: var(--ds-space-sm) var(--ds-space-md);
}

.ds-table-empty {
  color: var(--ds-color-text-muted);
  font-size: var(--ds-font-size-sm);
}

.ds-table-pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--ds-space-sm) var(--ds-space-md);
  border-top: 1px solid var(--ds-color-border);
  background-color: var(--ds-color-surface-raised);
  font-size: var(--ds-font-size-xs);
  color: var(--ds-color-text-muted);
}

.ds-table-pagination__controls {
  display: flex;
  align-items: center;
  gap: var(--ds-space-sm);
}

.ds-table-pagination__btn {
  background: none;
  border: 1px solid var(--ds-color-border);
  border-radius: var(--ds-radius-sm);
  padding: 2px var(--ds-space-xs);
  cursor: pointer;
  color: var(--ds-color-text);
  font-size: var(--ds-font-size-md);
  line-height: 1;
  transition: background-color var(--ds-duration-fast) var(--ds-ease-standard);
}

.ds-table-pagination__btn:hover:not(:disabled) {
  background-color: var(--ds-color-surface-raised);
}

.ds-table-pagination__btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.ds-table-pagination__pages {
  min-width: 60px;
  text-align: center;
}
</style>
