<template>
    <wa-icon :name="name" :label="label" :size="size" :animation="animation" />
</template>

<script setup lang="ts">
import '@awesome.me/webawesome/dist/components/icon/icon.js'
import type { IconAnimation } from '@awesome.me/webawesome/dist/components/icon/icon.js'

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
