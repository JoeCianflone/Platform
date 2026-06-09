<script lang="ts" setup>
// Imports ............................................................
import '@awesome.me/webawesome/dist/components/checkbox/checkbox.js'
import { computed } from 'vue'
import type { AppSize, InertiaForm } from '@ds/types/ui'

// Props & Emits ......................................................
const props = withDefaults(
  defineProps<{
    form: InertiaForm
    field: string
    label?: string
    hint?: string
    required?: boolean
    disabled?: boolean
    size?: AppSize
  }>(),
  { required: false, disabled: false, size: 'md' }
)

// Computed ...........................................................
const waSize = computed(() => {
  const map: Record<AppSize, string> = { sm: 's', md: 'm', lg: 'l' }
  return map[props.size]
})
const fieldValue = computed(() => Boolean(props.form[props.field]))
const errorMessage = computed(() => {
  const err = props.form.errors[props.field]
  if (!err) return undefined
  return Array.isArray(err) ? err[0] : err
})

// Methods / Event Handlers ...........................................
function handleChange(e: Event): void {
  props.form[props.field] = (e.target as HTMLInputElement).checked
  props.form.clearErrors(props.field)
}
</script>

<template>
  <wa-checkbox
    :checked="fieldValue"
    :hint="errorMessage ?? hint"
    :required="required"
    :disabled="disabled"
    :size="waSize"
    :class="{ 'ds-field--error': !!errorMessage }"
    @change="handleChange"
  >
    {{ label }}
  </wa-checkbox>
</template>

<style scoped>
.ds-field--error::part(hint) {
  color: var(--ds-color-danger);
}
</style>
