<script lang="ts" setup>
// Imports ............................................................
import '@awesome.me/webawesome/dist/components/badge/badge.js'
import { computed } from 'vue'
import type { AppVariant } from '@ds/types/ui'

// Props & Emits ......................................................
const props = withDefaults(
  defineProps<{
    variant?: AppVariant
    appearance?: 'accent' | 'filled' | 'outlined' | 'filled-outlined'
    pill?: boolean
    attention?: 'none' | 'pulse' | 'bounce'
  }>(),
  { variant: 'primary', appearance: 'filled', pill: false, attention: 'none' }
)

// Computed ...........................................................
const waVariant = computed(() => {
  const map: Record<AppVariant, string> = {
    primary: 'brand',
    secondary: 'neutral',
    success: 'success',
    warning: 'warning',
    danger: 'danger',
    neutral: 'neutral',
  }
  return map[props.variant]
})
</script>

<template>
  <wa-badge :variant="waVariant" :appearance="appearance" :pill="pill" :attention="attention">
    <slot name="start" slot="start" />
    <slot />
    <slot name="end" slot="end" />
  </wa-badge>
</template>
