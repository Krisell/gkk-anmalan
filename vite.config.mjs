import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import createVuePlugin from '@vitejs/plugin-vue'

export default defineConfig({
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
  },
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js',
    },
  }
})
