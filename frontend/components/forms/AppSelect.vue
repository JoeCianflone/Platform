<template>
    <wa-select
        :value="fieldValue"
        :label="label"
        :placeholder="placeholder"
        :hint="errorMessage ?? hint"
        :required="required"
        :disabled="disabled"
        :multiple="multiple"
        :size="waSize"
        :class="{ 'ds-field--error': !!errorMessage }"
        @change="handleChange"
    >
        <slot />
    </wa-select>
</template>

<script setup lang="ts">
import '@awesome.me/webawesome/dist/components/select/select.js'
import '@awesome.me/webawesome/dist/components/option/option.js'
import { computed } from 'vue'
import type { AppSize, InertiaForm } from '@/ds/ui'

const props = withDefaults(
    defineProps<{
        form: InertiaForm
        field: string
        label?: string
        placeholder?: string
        hint?: string
        required?: boolean
        disabled?: boolean
        multiple?: boolean
        size?: AppSize
    }>(),
    { required: false, disabled: false, multiple: false, size: 'md' },
)

const waSize = computed(() => {
    const map: Record<AppSize, string> = { sm: 's', md: 'm', lg: 'l' }
    return map[props.size]
})

const fieldValue = computed(() => {
    const val = props.form[props.field]
    return (val as string | string[] | null) ?? ''
})

const errorMessage = computed(() => {
    const err = props.form.errors[props.field]
    if (!err) return undefined
    return Array.isArray(err) ? err[0] : err
})

function handleChange(e: Event): void {
    const target = e.target as HTMLSelectElement & { value: string | string[] }
    props.form[props.field] = target.value
    props.form.clearErrors(props.field)
}
</script>

<style scoped>
.ds-field--error::part(hint) {
    color: var(--ds-color-danger);
}

.ds-field--error::part(combobox) {
    border-color: var(--ds-color-danger);
}
</style>
