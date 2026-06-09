import { defineConfig } from 'vitest/config'
import { fileURLToPath, URL } from 'node:url'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('.', import.meta.url)),
      '@ds': fileURLToPath(new URL('../ds', import.meta.url)),
    },
  },
  test: {
    environment: 'happy-dom',
    globals: true,
  },
})
