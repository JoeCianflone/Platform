<template>
    <div class="ds-shell" :class="{ 'ds-shell--collapsed': collapsed }">
        <aside class="ds-shell__sidebar">
            <slot name="sidebar" />
        </aside>
        <div class="ds-shell__body">
            <header v-if="$slots.header" class="ds-shell__header">
                <slot name="header" />
            </header>
            <main class="ds-shell__content">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
withDefaults(
    defineProps<{
        collapsed?: boolean
    }>(),
    { collapsed: false },
)
</script>

<style scoped>
.ds-shell {
    display: grid;
    grid-template-columns: var(--ds-shell-sidebar-width, 260px) 1fr;
    min-height: 100vh;
}

.ds-shell--collapsed {
    grid-template-columns: var(--ds-shell-sidebar-collapsed-width, 64px) 1fr;
}

.ds-shell__sidebar {
    background-color: var(--ds-color-surface-raised);
    border-right: 1px solid var(--ds-color-border);
    overflow-y: auto;
    position: sticky;
    top: 0;
    height: 100vh;
    transition: width var(--ds-duration-normal) var(--ds-ease-standard);
}

.ds-shell__body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    overflow: hidden;
}

.ds-shell__header {
    background-color: var(--ds-color-surface-raised);
    border-bottom: 1px solid var(--ds-color-border);
    padding: var(--ds-space-md) var(--ds-space-lg);
    position: sticky;
    top: 0;
    z-index: 10;
}

.ds-shell__content {
    flex: 1;
    padding: var(--ds-space-lg);
    overflow-y: auto;
}
</style>
