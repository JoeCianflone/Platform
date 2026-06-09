import { defineConfig } from 'vite'
import { fileURLToPath, URL } from 'node:url'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    vue(),
    laravel({
      input: ['app.ts'],
      refresh: false,
      publicDirectory: '../../public',
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('.', import.meta.url)),
      '@ds': fileURLToPath(new URL('../ds', import.meta.url)),
    },
  },
  build: {
    emptyOutDir: true,
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes('node_modules')) {
            if (id.includes('@awesome.me/webawesome')) return 'webawesome'
            if (id.includes('@inertiajs/vue3')) return 'inertia'
            if (id.includes('/vue/') || id.includes('/vue-demi') || id.includes('@vue/'))
              return 'vue'
          }
        },
      },
    },
  },
  server: {
    watch: {},
  },
  test: {
    environment: 'happy-dom',
    globals: true,
  },
})
