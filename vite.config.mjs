import { defineConfig, lazyPlugins } from 'vite-plus'
import laravel from 'laravel-vite-plugin'
import createVuePlugin from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  staged: {
    '*': 'vp check --fix',
  },
  lint: {
    ignorePatterns: ['vendor', 'node_modules', 'public'],
    options: {
      typeAware: true,
      typeCheck: true,
    },
    jsPlugins: [
      {
        name: 'vite-plus',
        specifier: 'vite-plus/oxlint-plugin',
      },
    ],
    rules: {
      'vite-plus/prefer-vite-plus-imports': 'error',
    },
  },
  fmt: {
    trailingComma: 'all',
    semi: false,
    singleQuote: true,
    printWidth: 120,
    sortPackageJson: false,
    ignorePatterns: [],
  },
  plugins: lazyPlugins(() => [
    tailwindcss(),
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
  ]),
  test: {
    globals: true,
    environment: 'jsdom',
    include: ['tests/client/**/*.{test,spec}.{js,mjs,cjs,ts,mts,cts,jsx,tsx}'],
  },
  build: {
    chunkSizeWarningLimit: 2048,
  },
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js',
    },
  },
})
