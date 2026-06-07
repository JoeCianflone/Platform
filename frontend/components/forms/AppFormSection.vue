<template>
    <fieldset class="ds-form-section">
        <legend v-if="title || $slots.header" class="ds-form-section__legend">
            <slot name="header">
                <span class="ds-form-section__title">{{ title }}</span>
                <span v-if="description" class="ds-form-section__description">{{ description }}</span>
            </slot>
        </legend>
        <div class="ds-form-section__body">
            <slot />
        </div>
    </fieldset>
</template>

<script setup lang="ts">
withDefaults(
    defineProps<{
        title?: string
        description?: string
    }>(),
    {},
)
</script>

<style scoped>
.ds-form-section {
    border: none;
    padding: 0;
    margin: 0;
    display: contents;
}

.ds-form-section__legend {
    display: block;
    width: 100%;
    margin-bottom: var(--ds-space-md);
}

.ds-form-section__title {
    display: block;
    font-size: var(--ds-font-size-md);
    font-weight: 600;
    color: var(--ds-color-text);
}

.ds-form-section__description {
    display: block;
    font-size: var(--ds-font-size-sm);
    color: var(--ds-color-text-muted);
    margin-top: var(--ds-space-xs);
}

.ds-form-section__body {
    display: flex;
    flex-direction: column;
    gap: var(--ds-space-md);
}
</style>
