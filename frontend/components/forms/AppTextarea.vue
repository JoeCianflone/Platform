<template>
    <wa-textarea
        :value="fieldValue"
        :label="label"
        :placeholder="placeholder"
        :hint="errorMessage ?? hint"
        :rows="rows"
        :required="required"
        :disabled="disabled"
        :readonly="readonly"
        :size="waSize"
        :class="{ 'ds-field--error': !!errorMessage }"
        @input="handleInput"
        @change="handleChange"
    />
</template>

<script setup lang="ts">
import '@awesome.me/webawesome/dist/components/textarea/textarea.js'
import { computed } from 'vue'
import type { AppSize, InertiaForm } from '@/ds/ui'

const props = withDefaults(
    defineProps<{
        form: InertiaForm
        field: string
        label?: string
        placeholder?: string
        hint?: string
        rows?: number
        required?: boolean
        disabled?: boolean
        readonly?: boolean
        size?: AppSize
    }>(),
    { rows: 4, required: false, disabled: false, readonly: false, size: 'md' },
)

const waSize = computed(() => {
    const map: Record<AppSize, string> = { sm: 's', md: 'm', lg: 'l' }
    return map[props.size]
})

const fieldValue = computed(() => (props.form[props.field] as string | null) ?? '')

const errorMessage = computed(() => {
    const err = props.form.errors[props.field]
    if (!err) return undefined
    return Array.isArray(err) ? err[0] : err
})

function handleInput(e: Event): void {
    props.form[props.field] = (e.target as HTMLTextAreaElement).value
    props.form.clearErrors(props.field)
}

function handleChange(e: Event): void {
    props.form[props.field] = (e.target as HTMLTextAreaElement).value
}
</script>

<style scoped>
.ds-field--error::part(hint) {
    color: var(--ds-color-danger);
}

.ds-field--error::part(base) {
    border-color: var(--ds-color-danger);
}
</style>
