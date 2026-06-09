<script lang="ts" setup>
// Imports ............................................................
import '@awesome.me/webawesome/dist/components/input/input.js'
import { computed } from 'vue'
import type { AppSize, InertiaForm } from '@ds/types/ui'

// Props & Emits ......................................................
const props = withDefaults(
  defineProps<{
    form: InertiaForm
    field: string
    label?: string
    type?: 'text' | 'email' | 'password' | 'number' | 'tel' | 'url' | 'search' | 'date'
    placeholder?: string
    hint?: string
    required?: boolean
    disabled?: boolean
    readonly?: boolean
    size?: AppSize
  }>(),
  { type: 'text', required: false, disabled: false, readonly: false, size: 'md' }
)

// Computed ...........................................................
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

// Methods / Event Handlers ...........................................
function handleInput(e: Event): void {
  props.form[props.field] = (e.target as HTMLInputElement).value
  props.form.clearErrors(props.field)
}
function handleChange(e: Event): void {
  props.form[props.field] = (e.target as HTMLInputElement).value
}
</script>

<template>
  <wa-input
    :value="fieldValue"
    :label="label"
    :type="type"
    :placeholder="placeholder"
    :hint="errorMessage ?? hint"
    :required="required"
    :disabled="disabled"
    :readonly="readonly"
    :size="waSize"
    :class="{ 'ds-field--error': !!errorMessage }"
    @input="handleInput"
    @change="handleChange"
  />
</template>

<style scoped>
.ds-field--error::part(hint) {
  color: var(--ds-color-danger);
}

.ds-field--error::part(base) {
  border-color: var(--ds-color-danger);
}
</style>
