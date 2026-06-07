<template>
    <template v-if="isPremium">
        <slot />
    </template>
    <template v-else>
        <slot name="fallback">
            <div class="ds-premium-gate">
                <p class="ds-premium-gate__label">Premium feature</p>
                <p class="ds-premium-gate__description">
                    Upgrade your plan to access this feature.
                </p>
                <slot name="upgrade" />
            </div>
        </slot>
    </template>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import type { SharedProps } from '@/ds/shared'

const page = usePage<SharedProps>()
const isPremium = computed(() => page.props.auth.user?.is_premium === true)
</script>

<style scoped>
.ds-premium-gate {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--ds-space-sm);
    padding: var(--ds-space-xl);
    border: 1px dashed var(--ds-color-border);
    border-radius: var(--ds-radius-md);
    text-align: center;
}

.ds-premium-gate__label {
    margin: 0;
    font-size: var(--ds-font-size-sm);
    font-weight: 600;
    color: var(--ds-color-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.ds-premium-gate__description {
    margin: 0;
    font-size: var(--ds-font-size-sm);
    color: var(--ds-color-text-muted);
}
</style>
