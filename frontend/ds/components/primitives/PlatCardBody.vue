<template>
    <div class="ds-card-body">
        <slot />
    </div>
</template>

<style scoped>
.ds-card-body {
    display: flex;
    flex-direction: column;
    gap: var(--ds-space-md);
}
</style>
