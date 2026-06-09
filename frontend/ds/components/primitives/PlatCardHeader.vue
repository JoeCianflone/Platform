<template>
  <header slot="header" class="ds-card-header">
    <slot />
  </header>
</template>

<style scoped>
.ds-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--ds-space-sm);
}
</style>
