import './app.css'
import { createApp, h, type DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { usePlatTheme } from '@ds/composables/usePlatTheme'

const pages = import.meta.glob<{ default: DefineComponent }>('./pages/**/*.vue')

document.documentElement.classList.add('wa-theme-awesome')
usePlatTheme().init()

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
