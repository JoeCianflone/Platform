import type { AppTheme } from '@ds/types/ui'

const STORAGE_KEY = 'ds-theme'

function getSystemTheme(): AppTheme {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
}

function apply(theme: AppTheme): void {
    const html = document.documentElement
    if (theme === 'dark') {
        html.classList.add('wa-dark')
        html.classList.remove('wa-light')
    } else {
        html.classList.remove('wa-dark')
        html.classList.add('wa-light')
    }
    localStorage.setItem(STORAGE_KEY, theme)
}

function toggle(): void {
    const isDark = document.documentElement.classList.contains('wa-dark')
    apply(isDark ? 'light' : 'dark')
}

function current(): AppTheme {
    return document.documentElement.classList.contains('wa-dark') ? 'dark' : 'light'
}

function init(): void {
    const saved = localStorage.getItem(STORAGE_KEY) as AppTheme | null
    apply(saved ?? getSystemTheme())
}

export function usePlatTheme() {
    return { apply, toggle, current, init }
}
