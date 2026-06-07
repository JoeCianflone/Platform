<template>
    <div
        class="ds-cluster"
        :data-space="space"
        :data-align="align"
        :data-justify="justify"
        :data-wrap="wrap ? 'wrap' : 'nowrap'"
    >
        <slot />
    </div>
</template>

<script setup lang="ts">
withDefaults(
    defineProps<{
        space?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
        align?: 'start' | 'center' | 'end' | 'stretch' | 'baseline'
        justify?: 'start' | 'center' | 'end' | 'between' | 'around' | 'evenly'
        wrap?: boolean
    }>(),
    { space: 'md', align: 'center', justify: 'start', wrap: true },
)
</script>

<style scoped>
.ds-cluster {
    display: flex;
}

.ds-cluster[data-wrap='wrap']   { flex-wrap: wrap; }
.ds-cluster[data-wrap='nowrap'] { flex-wrap: nowrap; }

.ds-cluster[data-space='xs'] { gap: var(--ds-space-xs); }
.ds-cluster[data-space='sm'] { gap: var(--ds-space-sm); }
.ds-cluster[data-space='md'] { gap: var(--ds-space-md); }
.ds-cluster[data-space='lg'] { gap: var(--ds-space-lg); }
.ds-cluster[data-space='xl'] { gap: var(--ds-space-xl); }

.ds-cluster[data-align='start']    { align-items: flex-start; }
.ds-cluster[data-align='center']   { align-items: center; }
.ds-cluster[data-align='end']      { align-items: flex-end; }
.ds-cluster[data-align='stretch']  { align-items: stretch; }
.ds-cluster[data-align='baseline'] { align-items: baseline; }

.ds-cluster[data-justify='start']   { justify-content: flex-start; }
.ds-cluster[data-justify='center']  { justify-content: center; }
.ds-cluster[data-justify='end']     { justify-content: flex-end; }
.ds-cluster[data-justify='between'] { justify-content: space-between; }
.ds-cluster[data-justify='around']  { justify-content: space-around; }
.ds-cluster[data-justify='evenly']  { justify-content: space-evenly; }
</style>
