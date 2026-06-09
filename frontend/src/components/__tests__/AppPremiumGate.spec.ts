import { describe, it, expect } from 'vitest'
import { ref, computed } from 'vue'

describe('AppPremiumGate premium logic', () => {
  function isPremium(user: { is_premium: boolean } | null | undefined) {
    return computed(() => user?.is_premium === true)
  }

  it('returns true for premium user', () => {
    expect(isPremium({ is_premium: true }).value).toBe(true)
  })

  it('returns false for non-premium user', () => {
    expect(isPremium({ is_premium: false }).value).toBe(false)
  })

  it('returns false when user is null', () => {
    expect(isPremium(null).value).toBe(false)
  })

  it('returns false when user is undefined', () => {
    expect(isPremium(undefined).value).toBe(false)
  })

  it('is reactive', () => {
    const user = ref<{ is_premium: boolean } | null>({ is_premium: false })
    const gate = computed(() => user.value?.is_premium === true)
    expect(gate.value).toBe(false)
    user.value = { is_premium: true }
    expect(gate.value).toBe(true)
  })
})
