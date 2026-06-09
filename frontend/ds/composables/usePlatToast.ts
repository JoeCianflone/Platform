import { reactive } from 'vue'
import type { AppVariant } from '@ds/types/ui'

interface Toast {
    id: number
    message: string
    variant: AppVariant
    duration: number
}

interface ToastState {
    toasts: Toast[]
}

const state = reactive<ToastState>({ toasts: [] })
let nextId = 0

export function usePlatToast() {
    function show(
        message: string,
        variant: AppVariant = 'neutral',
        duration = 4000,
    ): void {
        const id = ++nextId
        state.toasts.push({ id, message, variant, duration })
        if (duration > 0) {
            setTimeout(() => dismiss(id), duration)
        }
    }

    function success(message: string, duration = 4000): void {
        show(message, 'success', duration)
    }

    function danger(message: string, duration = 4000): void {
        show(message, 'danger', duration)
    }

    function warning(message: string, duration = 4000): void {
        show(message, 'warning', duration)
    }

    function dismiss(id: number): void {
        const index = state.toasts.findIndex((t) => t.id === id)
        if (index !== -1) state.toasts.splice(index, 1)
    }

    return { state, show, success, danger, warning, dismiss }
}
