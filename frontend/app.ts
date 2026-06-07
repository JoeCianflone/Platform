import './app.css'
import { createApp, h, type DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { useTheme } from '@/composables/useTheme'

// Apply WA theme class and restore saved/system theme preference
document.documentElement.classList.add('wa-theme-awesome')
useTheme().init()

const pages = import.meta.glob<{ default: DefineComponent }>('./pages/**/*.vue')

createInertiaApp({
    resolve: async (name: string) => {
        const path = `./pages/${name}.vue`
        const page = pages[path]
        if (!page) throw new Error(`[App] Page not found: ${name}`)
        return (await page()).default
    },

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el!)
    },

    progress: {
        color: '#4B5563',
    },
})
