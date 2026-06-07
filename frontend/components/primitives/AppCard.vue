<template>
    <wa-card :appearance="appearance" :orientation="orientation">
        <slot />
    </wa-card>
</template>

<script setup lang="ts">
import '@awesome.me/webawesome/dist/components/card/card.js'

withDefaults(
    defineProps<{
        appearance?: 'accent' | 'filled' | 'outlined' | 'filled-outlined' | 'plain'
        orientation?: 'vertical' | 'horizontal'
    }>(),
    { appearance: 'outlined', orientation: 'vertical' },
)
</script>

<style scoped>
wa-card {
    --spacing: var(--ds-space-lg);
}
</style>
