<script lang="ts" setup>
// Imports ............................................................
import PlatCard from '@ds/components/primitives/PlatCard.vue'
import PlatCardBody from '@ds/components/primitives/PlatCardBody.vue'

// Props & Emits ......................................................
defineProps<{
  label: string
  value: string | number
  change?: number
}>()
</script>

<template>
  <PlatCard appearance="outlined" class="ds-stat-card">
    <PlatCardBody>
      <p class="ds-stat-card__label">{{ label }}</p>
      <p class="ds-stat-card__value">{{ value }}</p>
      <p
        v-if="change !== undefined"
        class="ds-stat-card__change"
        :class="change >= 0 ? 'ds-stat-card__change--positive' : 'ds-stat-card__change--negative'"
      >
        {{ change >= 0 ? '+' : '' }}{{ change }}%
      </p>
    </PlatCardBody>
  </PlatCard>
</template>

<style scoped>
.ds-stat-card__label {
  margin: 0;
  font-size: var(--ds-font-size-xs);
  font-weight: 500;
  color: var(--ds-color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.ds-stat-card__value {
  margin: 0;
  font-size: var(--ds-font-size-xl);
  font-weight: 700;
  color: var(--ds-color-text);
  line-height: 1.1;
}

.ds-stat-card__change {
  margin: 0;
  font-size: var(--ds-font-size-xs);
  font-weight: 500;
}

.ds-stat-card__change--positive {
  color: var(--ds-color-success);
}
.ds-stat-card__change--negative {
  color: var(--ds-color-danger);
}
</style>
