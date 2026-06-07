<template>
    <Teleport to="body">
        <div class="ds-toast-region" aria-live="polite" aria-atomic="false">
            <TransitionGroup name="toast">
                <div
                    v-for="toast in state.toasts"
                    :key="toast.id"
                    class="ds-toast-item"
                >
                    <wa-callout :variant="waVariant(toast.variant)" appearance="filled">
                        <slot />{{ toast.message }}
                    </wa-callout>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import '@awesome.me/webawesome/dist/components/callout/callout.js'
import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'
import type { AppVariant } from '@/ds/ui'
import type { SharedProps } from '@/ds/shared'

const page = usePage<SharedProps>()
const { state, success, danger } = useToast()

function waVariant(variant: AppVariant): string {
    const map: Record<AppVariant, string> = {
        primary: 'brand',
        secondary: 'neutral',
        success: 'success',
        warning: 'warning',
        danger: 'danger',
        neutral: 'neutral',
    }
    return map[variant]
}

// Surface Inertia flash + general errors as toasts on each navigation
watch(
    () => page.props.flash?.success,
    (msg) => {
        if (msg) success(msg)
    },
)

watch(
    () => (page.props.errors as Record<string, string | string[]>)?.general,
    (err) => {
        if (err) {
            const msg = Array.isArray(err) ? err[0] : err
            if (msg) danger(msg)
        }
    },
)
</script>

<style scoped>
.ds-toast-region {
    position: fixed;
    bottom: var(--ds-space-lg);
    right: var(--ds-space-lg);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: var(--ds-space-sm);
    pointer-events: none;
    max-width: 400px;
}

.ds-toast-item {
    pointer-events: auto;
}

.toast-enter-active,
.toast-leave-active {
    transition:
        opacity var(--ds-duration-normal) var(--ds-ease-standard),
        transform var(--ds-duration-normal) var(--ds-ease-standard);
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(var(--ds-space-md));
}
</style>
