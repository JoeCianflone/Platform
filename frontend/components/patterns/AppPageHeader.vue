<template>
    <div class="ds-page-header">
        <div class="ds-page-header__main">
            <h1 class="ds-page-header__title">{{ title }}</h1>
            <p v-if="description" class="ds-page-header__description">{{ description }}</p>
        </div>
        <div v-if="$slots.actions" class="ds-page-header__actions">
            <slot name="actions" />
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    title: string
    description?: string
}>()
</script>

<style scoped>
.ds-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: var(--ds-space-md);
}

.ds-page-header__main {
    display: flex;
    flex-direction: column;
    gap: var(--ds-space-xs);
}

.ds-page-header__title {
    margin: 0;
    font-size: var(--ds-font-size-xl);
    font-weight: 700;
    line-height: 1.2;
    color: var(--ds-color-text);
}

.ds-page-header__description {
    margin: 0;
    font-size: var(--ds-font-size-sm);
    color: var(--ds-color-text-muted);
}

.ds-page-header__actions {
    display: flex;
    align-items: center;
    gap: var(--ds-space-sm);
    flex-shrink: 0;
}
</style>
