<script lang="ts" setup>
// Imports ............................................................
import { usePlatConfirm } from '@ds/composables/usePlatConfirm'
import PlatDialog from '@ds/components/primitives/PlatDialog.vue'
import PlatButton from '@ds/components/primitives/PlatButton.vue'

// Methods / Event Handlers ...........................................
const { state, confirm, cancel } = usePlatConfirm()
</script>

<template>
    <PlatDialog v-model:open="state.open" :label="state.message ? 'Confirm' : ''">
        <p class="ds-confirm__message">{{ state.message }}</p>
        <template #footer>
            <div class="ds-confirm__actions">
                <PlatButton variant="neutral" appearance="outlined" @click="cancel">
                    {{ state.cancelLabel }}
                </PlatButton>
                <PlatButton variant="danger" @click="confirm">
                    {{ state.confirmLabel }}
                </PlatButton>
            </div>
        </template>

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
