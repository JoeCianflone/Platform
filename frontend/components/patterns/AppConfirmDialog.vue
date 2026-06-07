<template>
    <AppDialog v-model:open="state.open" :label="state.message ? 'Confirm' : ''">
        <p class="ds-confirm__message">{{ state.message }}</p>
        <template #footer>
            <div class="ds-confirm__actions">
                <AppButton variant="neutral" appearance="outlined" @click="cancel">
                    {{ state.cancelLabel }}
                </AppButton>
                <AppButton variant="danger" @click="confirm">
                    {{ state.confirmLabel }}
                </AppButton>
            </div>
        </template>
    </AppDialog>
</template>

<script setup lang="ts">
import { useConfirm } from '@/composables/useConfirm'
import AppDialog from '@/components/primitives/AppDialog.vue'
import AppButton from '@/components/primitives/AppButton.vue'

const { state, confirm, cancel } = useConfirm()
</script>

<style scoped>
.ds-confirm__message {
    margin: 0;
    font-size: var(--ds-font-size-md);
    color: var(--ds-color-text);
    line-height: 1.6;
}

.ds-confirm__actions {
    display: flex;
    justify-content: flex-end;
    gap: var(--ds-space-sm);
}
</style>
