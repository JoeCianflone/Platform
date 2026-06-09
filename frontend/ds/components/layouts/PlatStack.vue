<script lang="ts" setup>
// Props & Emits ......................................................
withDefaults(
    defineProps<{
        space?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl'
        align?: 'start' | 'center' | 'end' | 'stretch'
    }>(),
    { space: 'md', align: 'stretch' },
)
</script>

<template>
    <div class="ds-stack" :data-space="space" :data-align="align">
        <slot />
    </div>
</template>

<style scoped>
.ds-stack {
    display: flex;
    flex-direction: column;
}

.ds-stack[data-space='xs'] > * + * { margin-top: var(--ds-space-xs); }
.ds-stack[data-space='sm'] > * + * { margin-top: var(--ds-space-sm); }
.ds-stack[data-space='md'] > * + * { margin-top: var(--ds-space-md); }
.ds-stack[data-space='lg'] > * + * { margin-top: var(--ds-space-lg); }
.ds-stack[data-space='xl'] > * + * { margin-top: var(--ds-space-xl); }
.ds-stack[data-space='2xl'] > * + * { margin-top: var(--ds-space-2xl); }

.ds-stack[data-align='start']   { align-items: flex-start; }
.ds-stack[data-align='center']  { align-items: center; }
.ds-stack[data-align='end']     { align-items: flex-end; }
.ds-stack[data-align='stretch'] { align-items: stretch; }
</style>
