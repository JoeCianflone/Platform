<template>
  <div class="ds-page">
    <slot />
  </div>
</template>

<style scoped>
.ds-page {
  display: flex;
  flex-direction: column;
  gap: var(--ds-space-lg);
  max-width: var(--ds-page-max-width, 1280px);
  margin: 0 auto;
  width: 100%;
}
</style>
