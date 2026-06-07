<template>
    <component :is="tag" class="ds-text" :data-variant="variant">
        <slot />
    </component>
</template>

<script setup lang="ts">
withDefaults(
    defineProps<{
        tag?: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6' | 'p' | 'span' | 'label' | 'legend' | 'div'
        variant?: 'page-title' | 'section-title' | 'subsection-title' | 'body' | 'body-sm' | 'caption' | 'label'
    }>(),
    { tag: 'p', variant: 'body' },
)
</script>

<style scoped>
.ds-text[data-variant='page-title'] {
    font-size: var(--ds-font-size-xl);
    font-weight: 700;
    line-height: 1.2;
    color: var(--ds-color-text);
}

.ds-text[data-variant='section-title'] {
    font-size: var(--ds-font-size-lg);
    font-weight: 600;
    line-height: 1.3;
    color: var(--ds-color-text);
}

.ds-text[data-variant='subsection-title'] {
    font-size: var(--ds-font-size-md);
    font-weight: 600;
    line-height: 1.4;
    color: var(--ds-color-text);
}

.ds-text[data-variant='body'] {
    font-size: var(--ds-font-size-md);
    font-weight: 400;
    line-height: 1.6;
    color: var(--ds-color-text);
}

.ds-text[data-variant='body-sm'] {
    font-size: var(--ds-font-size-sm);
    font-weight: 400;
    line-height: 1.5;
    color: var(--ds-color-text);
}

.ds-text[data-variant='caption'] {
    font-size: var(--ds-font-size-xs);
    font-weight: 400;
    line-height: 1.4;
    color: var(--ds-color-text-muted);
}

.ds-text[data-variant='label'] {
    font-size: var(--ds-font-size-sm);
    font-weight: 500;
    line-height: 1.4;
    color: var(--ds-color-text);
}
</style>
