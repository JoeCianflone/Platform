<template>
    <AppCard appearance="outlined" class="ds-stat-card">
        <AppCardBody>
            <p class="ds-stat-card__label">{{ label }}</p>
            <p class="ds-stat-card__value">{{ value }}</p>
            <p
                v-if="change !== undefined"
                class="ds-stat-card__change"
                :class="change >= 0 ? 'ds-stat-card__change--positive' : 'ds-stat-card__change--negative'"
            >
                {{ change >= 0 ? '+' : '' }}{{ change }}%
            </p>
        </AppCardBody>
    </AppCard>
</template>

<script setup lang="ts">
import AppCard from '@/components/primitives/AppCard.vue'
import AppCardBody from '@/components/primitives/AppCardBody.vue'

defineProps<{
    label: string
    value: string | number
    change?: number
}>()
</script>

<style scoped>
.ds-stat-card__label {
    margin: 0;
    font-size: var(--ds-font-size-xs);
    font-weight: 500;
    color: var(--ds-color-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.ds-stat-card__value {
    margin: 0;
    font-size: var(--ds-font-size-xl);
    font-weight: 700;
    color: var(--ds-color-text);
    line-height: 1.1;
}

.ds-stat-card__change {
    margin: 0;
    font-size: var(--ds-font-size-xs);
    font-weight: 500;
}

.ds-stat-card__change--positive { color: var(--ds-color-success); }
.ds-stat-card__change--negative { color: var(--ds-color-danger); }
</style>
