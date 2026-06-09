import { reactive } from 'vue'

interface ConfirmState {
  open: boolean
  message: string
  confirmLabel: string
  cancelLabel: string
  resolve: ((value: boolean) => void) | null
}

const state = reactive<ConfirmState>({
  open: false,
  message: '',
  confirmLabel: 'Confirm',
  cancelLabel: 'Cancel',
  resolve: null,
})

export function usePlatConfirm() {
  function ask(message: string, options?: { confirm?: string; cancel?: string }): Promise<boolean> {
    return new Promise(resolve => {
      state.open = true
      state.message = message
      state.confirmLabel = options?.confirm ?? 'Confirm'
      state.cancelLabel = options?.cancel ?? 'Cancel'
      state.resolve = resolve
    })
  }

  function confirm(): void {
    state.resolve?.(true)
    state.open = false
    state.resolve = null
  }

  function cancel(): void {
    state.resolve?.(false)
    state.open = false
    state.resolve = null
  }

  return { state, ask, confirm, cancel }
}
