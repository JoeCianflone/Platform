<script lang="ts" setup>
// Imports ............................................................
import { computed } from 'vue'

// Props & Emits ......................................................
const props = withDefaults(
  defineProps<{
    space?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
    columns?: number
    minWidth?: string
  }>(),
  { space: 'md', minWidth: '240px' }
)

// Computed ...........................................................
const gridStyle = computed(() => {
  if (props.columns) {
    return { gridTemplateColumns: `repeat(${props.columns}, 1fr)` }
  }
  return { gridTemplateColumns: `repeat(auto-fill, minmax(${props.minWidth}, 1fr))` }
})
</script>

<template>
  <div class="ds-grid" :data-space="space" :style="gridStyle">
    <slot />
  </div>
</template>

<style scoped>
.ds-grid {
  display: grid;
}

.ds-grid[data-space='xs'] {
  gap: var(--ds-space-xs);
}
.ds-grid[data-space='sm'] {
  gap: var(--ds-space-sm);
}
.ds-grid[data-space='md'] {
  gap: var(--ds-space-md);
}
.ds-grid[data-space='lg'] {
  gap: var(--ds-space-lg);
}
.ds-grid[data-space='xl'] {
  gap: var(--ds-space-xl);
}
</style>
