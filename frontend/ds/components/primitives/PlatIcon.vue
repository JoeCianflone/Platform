<script lang="ts" setup>
// Imports ............................................................
import '@awesome.me/webawesome/dist/components/icon/icon.js'
import type { IconAnimation } from '@awesome.me/webawesome/dist/components/icon/icon.js'

// Props & Emits ......................................................
withDefaults(
    defineProps<{
        name: string
        label?: string
        size?: string
        animation?: IconAnimation
    }>(),
    { label: '' },
)
</script>

<template>
    <wa-icon :name="name" :label="label" :size="size" :animation="animation" />
</template>
