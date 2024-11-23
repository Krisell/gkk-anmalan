import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import createVuePlugin from '@vitejs/plugin-vue'

export default defineConfig({
  resolve: {
    alias: {
      vue: '@vue/compat'
    }
  },
  plugins: [
    process.env.VITEST
      ? null
      : laravel({
        valetTls: 'gkk.test',
        input: ['resources/css/main.css', 'resources/js/app.js'],
        refresh: true,
    }),
    createVuePlugin({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  test: {
    globals: true,
    environment: 'jsdom',
    include: ['tests/client/**/*.{test,spec}.{js,mjs,cjs,ts,mts,cts,jsx,tsx}'],
  },
  build: {
    chunkSizeWarningLimit: 1024,
  }
})
