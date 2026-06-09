<script lang="ts" setup>
// Props & Emits ......................................................
defineProps<{
  title: string
  description?: string
}>()
</script>

<template>
  <div class="ds-empty-state">
    <div v-if="$slots.icon" class="ds-empty-state__icon">
      <slot name="icon" />
    </div>
    <p class="ds-empty-state__title">{{ title }}</p>
    <p v-if="description" class="ds-empty-state__description">{{ description }}</p>
    <div v-if="$slots.default" class="ds-empty-state__actions">
      <slot />
    </div>
  </div>
</template>

<style scoped>
.ds-empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--ds-space-sm);
  padding: var(--ds-space-2xl) var(--ds-space-lg);
  text-align: center;
}

.ds-empty-state__icon {
  color: var(--ds-color-text-muted);
  font-size: 2.5rem;
  line-height: 1;
}

.ds-empty-state__title {
  margin: 0;
  font-size: var(--ds-font-size-md);
  font-weight: 600;
  color: var(--ds-color-text);
}

.ds-empty-state__description {
  margin: 0;
  font-size: var(--ds-font-size-sm);
  color: var(--ds-color-text-muted);
  max-width: 40ch;
}

.ds-empty-state__actions {
  margin-top: var(--ds-space-xs);
  display: flex;
  gap: var(--ds-space-sm);
}
</style>
