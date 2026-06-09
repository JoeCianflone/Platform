import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { usePage } from '@inertiajs/vue3'
import AppPremiumGate from '@/components/AppPremiumGate.vue'

vi.mock('@inertiajs/vue3', () => ({
  usePage: vi.fn(),
}))

type MockPage = ReturnType<typeof usePage>

function mockPage(isPremium: boolean) {
  vi.mocked(usePage).mockReturnValue({
    props: { auth: { user: { is_premium: isPremium } } },
  } as unknown as MockPage)
}

describe('AppPremiumGate', () => {
  it('renders slot content for premium users', () => {
    mockPage(true)
    const wrapper = mount(AppPremiumGate, { slots: { default: '<span>premium</span>' } })
    expect(wrapper.text()).toContain('premium')
  })

  it('hides slot content for non-premium users', () => {
    mockPage(false)
    const wrapper = mount(AppPremiumGate, { slots: { default: '<span>premium</span>' } })
    expect(wrapper.text()).not.toContain('premium')
  })

  it('hides slot content when user is null', () => {
    vi.mocked(usePage).mockReturnValue({ props: { auth: { user: null } } } as unknown as MockPage)
    const wrapper = mount(AppPremiumGate, { slots: { default: '<span>premium</span>' } })
    expect(wrapper.text()).not.toContain('premium')
  })
})
