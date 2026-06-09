<script lang="ts" setup>
// Imports ............................................................
import '@awesome.me/webawesome/dist/components/dialog/dialog.js'

// Props & Emits ......................................................
withDefaults(
    defineProps<{
        open?: boolean
        label?: string
        lightDismiss?: boolean
    }>(),
    { open: false, label: '', lightDismiss: true },
)
const emit = defineEmits<{
    'update:open': [value: boolean]
    'after-show': []
    'after-hide': []
}>()
</script>

<template>
    <wa-dialog
        :open="open"
        :label="label"
        :light-dismiss="lightDismiss"
        @wa-hide="emit('update:open', false)"
        @wa-after-hide="emit('after-hide')"
        @wa-after-show="emit('after-show')"
    >
        <div v-if="$slots['header-actions']" slot="header-actions">
            <slot name="header-actions" />
        </div>
        <slot />
        <div v-if="$slots.footer" slot="footer">
            <slot name="footer" />
        </div>
    </wa-dialog>
</template>

<style scoped>
wa-dialog {
    --width: var(--ds-dialog-width, 480px);
    --spacing: var(--ds-space-lg);
}
</style>
