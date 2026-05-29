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
      publicDirectory: '../public',
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('.', import.meta.url)),
    },
  },
  build: {
    emptyOutDir: true,
  },
  server: {
    watch: {
      ignored: ['**/storage/framework/views/**'],
    },
  },
})
