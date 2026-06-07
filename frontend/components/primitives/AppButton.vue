<template>
    <wa-button
        :variant="waVariant"
        :appearance="appearance"
        :size="waSize"
        :type="type"
        :href="href"
        :target="target"
        :loading="isLoading"
        :disabled="disabled || isLoading"
        :pill="pill"
    >
        <slot name="start" slot="start" />
        <slot />
        <slot name="end" slot="end" />
    </wa-button>
</template>

<script setup lang="ts">
import '@awesome.me/webawesome/dist/components/button/button.js'
import { computed } from 'vue'
import type { AppVariant, AppSize, InertiaForm } from '@/ds/ui'

const props = withDefaults(
    defineProps<{
        variant?: AppVariant
        appearance?: 'accent' | 'filled' | 'outlined' | 'plain'
        size?: AppSize
        type?: 'button' | 'submit' | 'reset'
        href?: string
        target?: '_blank' | '_self' | '_parent' | '_top'
        loading?: boolean
        disabled?: boolean
        pill?: boolean
        form?: InertiaForm
    }>(),
    {
        variant: 'primary',
        appearance: 'filled',
        size: 'md',
        type: 'button',
        loading: false,
        disabled: false,
        pill: false,
    },
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

const waSize = computed(() => {
    const map: Record<AppSize, string> = { sm: 's', md: 'm', lg: 'l' }
    return map[props.size]
})

const isLoading = computed(() => props.loading || props.form?.processing === true)
</script>
