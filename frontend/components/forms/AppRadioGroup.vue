<template>
    <wa-radio-group
        :value="fieldValue"
        :label="label"
        :hint="errorMessage ?? hint"
        :required="required"
        :disabled="disabled"
        :orientation="orientation"
        :size="waSize"
        :class="{ 'ds-field--error': !!errorMessage }"
        @change="handleChange"
    >
        <slot />
    </wa-radio-group>
</template>

<script setup lang="ts">
import '@awesome.me/webawesome/dist/components/radio-group/radio-group.js'
import '@awesome.me/webawesome/dist/components/radio/radio.js'
import { computed } from 'vue'
import type { AppSize, InertiaForm } from '@/ds/ui'

const props = withDefaults(
    defineProps<{
        form: InertiaForm
        field: string
        label?: string
        hint?: string
        required?: boolean
        disabled?: boolean
        orientation?: 'horizontal' | 'vertical'
        size?: AppSize
    }>(),
    { required: false, disabled: false, orientation: 'vertical', size: 'md' },
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

function handleChange(e: Event): void {
    const target = e.target as HTMLInputElement & { value: string }
    props.form[props.field] = target.value
    props.form.clearErrors(props.field)
}
</script>

<style scoped>
.ds-field--error::part(hint) {
    color: var(--ds-color-danger);
}
</style>
