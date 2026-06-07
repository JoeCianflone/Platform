<template>
    <div class="ds-filter-bar">
        <div class="ds-filter-bar__filters">
            <slot name="filters" />
        </div>
        <div v-if="$slots.actions" class="ds-filter-bar__actions">
            <slot name="actions" />
        </div>
    </div>
</template>

<style scoped>
.ds-filter-bar {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: var(--ds-space-sm);
    flex-wrap: wrap;
}

.ds-filter-bar__filters {
    display: flex;
    align-items: flex-end;
    gap: var(--ds-space-sm);
    flex-wrap: wrap;
    flex: 1;
}

.ds-filter-bar__actions {
    display: flex;
    align-items: center;
    gap: var(--ds-space-sm);
    flex-shrink: 0;
}
</style>
