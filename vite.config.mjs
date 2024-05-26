import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue2'

export default defineConfig({
  plugins: [
    process.env.VITEST
      ? null
      : laravel({
        valetTls: 'gkk.test',
        input: ['resources/css/main.css', 'resources/js/app.js'],
        refresh: true,
    }),
    vue({
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
    include: ['resources/js/test/**/*.{test,spec}.{js,mjs,cjs,ts,mts,cts,jsx,tsx}'],
  },
})
