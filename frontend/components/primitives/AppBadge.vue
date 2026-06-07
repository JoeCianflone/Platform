<template>
    <wa-badge :variant="waVariant" :appearance="appearance" :pill="pill" :attention="attention">
        <slot name="start" slot="start" />
        <slot />
        <slot name="end" slot="end" />
    </wa-badge>
</template>

<script setup lang="ts">
import '@awesome.me/webawesome/dist/components/badge/badge.js'
import { computed } from 'vue'
import type { AppVariant } from '@/ds/ui'

const props = withDefaults(
    defineProps<{
        variant?: AppVariant
        appearance?: 'accent' | 'filled' | 'outlined' | 'filled-outlined'
        pill?: boolean
        attention?: 'none' | 'pulse' | 'bounce'
    }>(),
    { variant: 'primary', appearance: 'filled', pill: false, attention: 'none' },
)

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
