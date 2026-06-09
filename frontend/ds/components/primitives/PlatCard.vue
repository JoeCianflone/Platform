<script lang="ts" setup>
// Imports ............................................................
import '@awesome.me/webawesome/dist/components/card/card.js'

// Props & Emits ......................................................
withDefaults(
    defineProps<{
        appearance?: 'accent' | 'filled' | 'outlined' | 'filled-outlined' | 'plain'
        orientation?: 'vertical' | 'horizontal'
    }>(),
    { appearance: 'outlined', orientation: 'vertical' },
)
</script>

<template>
    <wa-card :appearance="appearance" :orientation="orientation">
        <slot />
    </wa-card>
</template>

<style scoped>
wa-card {
    --spacing: var(--ds-space-lg);
}
</style>
