<template>
  <footer slot="footer" class="ds-card-footer">
    <slot />
  </footer>
</template>

<style scoped>
.ds-card-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: var(--ds-space-sm);
}
</style>
